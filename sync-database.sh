#!/bin/bash

# ============================================================================
# SCRIPT DE SYNCHRONISATION DE BASE DE DONNÉES LOCAL → PRODUCTION
# ============================================================================
# Ce script permet de synchroniser la base de données locale vers la production
# en préservant les données utilisateurs existantes
# ============================================================================

set -e  # Arrêter en cas d'erreur

# Configuration
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
BACKUP_DIR="$SCRIPT_DIR/storage/backups"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)

# Couleurs pour les messages
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Fonction pour afficher les messages
log_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

log_success() {
    echo -e "${GREEN}[✓]${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}[!]${NC} $1"
}

log_error() {
    echo -e "${RED}[✗]${NC} $1"
}

# Fonction pour créer un backup
create_backup() {
    local env=$1
    local backup_file="$2"
    
    if [ "$env" == "local" ]; then
        log_info "Création du backup local..."
        
        # Utilisation de Docker si disponible
        if docker compose ps | grep -q "laravel.test"; then
            docker compose exec -T mysql mysqldump -u root -ppassword soboa_foot_time > "$backup_file"
        else
            # Utilisation directe si MySQL local
            mysqldump -h 127.0.0.1 -P 3306 -u root -ppassword soboa_foot_time > "$backup_file"
        fi
    else
        log_info "Création du backup production..."
        
        # Récupération des credentials depuis .env.production
        if [ -f ".env.production" ]; then
            source .env.production
            mysqldump -h "$DB_HOST" -P "${DB_PORT:-3306}" -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" > "$backup_file"
        else
            log_error ".env.production non trouvé!"
            return 1
        fi
    fi
    
    log_success "Backup créé: $backup_file"
}

# Fonction pour restaurer un backup
restore_backup() {
    local env=$1
    local backup_file="$2"
    
    if [ ! -f "$backup_file" ]; then
        log_error "Fichier de backup non trouvé: $backup_file"
        return 1
    fi
    
    if [ "$env" == "production" ]; then
        log_warning "⚠️  ATTENTION: Vous êtes sur le point d'écraser la base de production!"
        read -p "Êtes-vous sûr de vouloir continuer? (oui/non): " confirmation
        
        if [ "$confirmation" != "oui" ]; then
            log_info "Opération annulée"
            return 0
        fi
        
        log_info "Restauration en production..."
        
        if [ -f ".env.production" ]; then
            source .env.production
            mysql -h "$DB_HOST" -P "${DB_PORT:-3306}" -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" < "$backup_file"
        else
            log_error ".env.production non trouvé!"
            return 1
        fi
    fi
    
    log_success "Base de données restaurée!"
}

# Menu principal
show_menu() {
    echo "=============================================="
    echo "   SYNCHRONISATION BASE DE DONNÉES"
    echo "=============================================="
    echo "1. 📦 Backup local uniquement"
    echo "2. 📦 Backup production uniquement"
    echo "3. 🔄 Sync COMPLET: Local → Production (écrase tout)"
    echo "4. 🔄 Sync SAFE: Local → Production (préserve users)"
    echo "5. 📊 Sync DONNÉES: Teams, Matchs, PDV → Production"
    echo "6. 🔍 Comparer Local vs Production"
    echo "7. ❌ Quitter"
    echo "=============================================="
}

# Fonction de sync complète
sync_full() {
    log_info "Démarrage de la synchronisation complète..."
    
    # Créer le dossier backup si nécessaire
    mkdir -p "$BACKUP_DIR"
    
    # 1. Backup de production
    PROD_BACKUP="$BACKUP_DIR/production_backup_$TIMESTAMP.sql"
    create_backup "production" "$PROD_BACKUP"
    
    # 2. Backup local
    LOCAL_BACKUP="$BACKUP_DIR/local_export_$TIMESTAMP.sql"
    create_backup "local" "$LOCAL_BACKUP"
    
    # 3. Restauration en production
    restore_backup "production" "$LOCAL_BACKUP"
    
    log_success "✅ Synchronisation complète terminée!"
    log_info "Backup production sauvegardé: $PROD_BACKUP"
}

# Fonction de sync sécurisée (préserve les utilisateurs)
sync_safe() {
    log_info "Démarrage de la synchronisation sécurisée..."
    
    # Créer le dossier backup si nécessaire
    mkdir -p "$BACKUP_DIR"
    
    # 1. Backup de production
    PROD_BACKUP="$BACKUP_DIR/production_backup_$TIMESTAMP.sql"
    create_backup "production" "$PROD_BACKUP"
    
    # 2. Export des données locales (sans users)
    LOCAL_EXPORT="$BACKUP_DIR/local_data_export_$TIMESTAMP.sql"
    log_info "Export des données locales (sans users)..."
    
    if docker compose ps | grep -q "laravel.test"; then
        docker compose exec -T mysql mysqldump -u root -ppassword soboa_foot_time \
            --ignore-table=soboa_foot_time.users \
            --ignore-table=soboa_foot_time.predictions \
            --ignore-table=soboa_foot_time.point_logs \
            --ignore-table=soboa_foot_time.password_reset_tokens \
            --ignore-table=soboa_foot_time.personal_access_tokens \
            --ignore-table=soboa_foot_time.sessions \
            > "$LOCAL_EXPORT"
    else
        mysqldump -h 127.0.0.1 -P 3306 -u root -ppassword soboa_foot_time \
            --ignore-table=soboa_foot_time.users \
            --ignore-table=soboa_foot_time.predictions \
            --ignore-table=soboa_foot_time.point_logs \
            --ignore-table=soboa_foot_time.password_reset_tokens \
            --ignore-table=soboa_foot_time.personal_access_tokens \
            --ignore-table=soboa_foot_time.sessions \
            > "$LOCAL_EXPORT"
    fi
    
    log_success "Export créé: $LOCAL_EXPORT"
    
    # 3. Appliquer en production via SSH
    log_info "Application des données en production..."
    log_warning "Cette opération va supprimer les teams, matchs et PDV existants!"
    
    read -p "Continuer? (oui/non): " confirmation
    if [ "$confirmation" == "oui" ]; then
        # Ici, vous devez adapter selon votre méthode d'accès à la production
        # Option 1: Via SSH
        # ssh user@production "mysql -u dbuser -p dbname < /path/to/export.sql"
        
        # Option 2: Via Forge/Deployer
        # php deployer.phar db:import "$LOCAL_EXPORT"
        
        log_info "Veuillez exécuter sur le serveur de production:"
        echo "mysql -u DB_USER -p DB_NAME < $LOCAL_EXPORT"
    fi
    
    log_success "✅ Synchronisation sécurisée préparée!"
    log_info "Backup production: $PROD_BACKUP"
    log_info "Export local: $LOCAL_EXPORT"
}

# Fonction de sync des données uniquement
sync_data_only() {
    log_info "Synchronisation des données de planning uniquement..."
    
    # Utilisation du seeder Laravel
    if docker compose ps | grep -q "laravel.test"; then
        log_info "Exécution du ProductionSyncSeeder via Docker..."
        docker compose exec laravel.test bash -c "cd /app && php artisan db:seed --class=ProductionSyncSeeder --force"
    else
        log_info "Exécution du ProductionSyncSeeder..."
        php artisan db:seed --class=ProductionSyncSeeder --force
    fi
    
    log_success "✅ Données synchronisées via seeder!"
}

# Fonction de comparaison
compare_databases() {
    log_info "Comparaison Local vs Production..."
    
    # Créer des dumps temporaires
    TEMP_DIR="/tmp/db_compare_$TIMESTAMP"
    mkdir -p "$TEMP_DIR"
    
    # Dump local (structure uniquement)
    log_info "Export structure locale..."
    if docker compose ps | grep -q "laravel.test"; then
        docker compose exec -T mysql mysqldump -u root -ppassword soboa_foot_time --no-data > "$TEMP_DIR/local_structure.sql"
    else
        mysqldump -h 127.0.0.1 -P 3306 -u root -ppassword soboa_foot_time --no-data > "$TEMP_DIR/local_structure.sql"
    fi
    
    # Dump production (structure uniquement)
    log_info "Export structure production..."
    if [ -f ".env.production" ]; then
        source .env.production
        mysqldump -h "$DB_HOST" -P "${DB_PORT:-3306}" -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" --no-data > "$TEMP_DIR/prod_structure.sql"
    fi
    
    # Comparer les fichiers
    log_info "Analyse des différences..."
    diff "$TEMP_DIR/local_structure.sql" "$TEMP_DIR/prod_structure.sql" > "$TEMP_DIR/diff.txt" || true
    
    if [ -s "$TEMP_DIR/diff.txt" ]; then
        log_warning "Des différences ont été trouvées:"
        head -50 "$TEMP_DIR/diff.txt"
        log_info "Fichier complet: $TEMP_DIR/diff.txt"
    else
        log_success "Les structures sont identiques!"
    fi
    
    # Statistiques
    log_info ""
    log_info "📊 STATISTIQUES:"
    log_info "=================="
    
    # Local stats
    if docker compose ps | grep -q "laravel.test"; then
        echo -e "${BLUE}LOCAL:${NC}"
        docker compose exec -T mysql mysql -u root -ppassword soboa_foot_time -e "
            SELECT 'Users' as Table_Name, COUNT(*) as Count FROM users
            UNION SELECT 'Teams', COUNT(*) FROM teams
            UNION SELECT 'Matches', COUNT(*) FROM matches
            UNION SELECT 'Venues', COUNT(*) FROM bars
            UNION SELECT 'Predictions', COUNT(*) FROM predictions
            UNION SELECT 'Animations', COUNT(*) FROM animations;
        "
    fi
    
    # Production stats (si accessible)
    if [ -f ".env.production" ]; then
        echo -e "${BLUE}PRODUCTION:${NC}"
        source .env.production
        mysql -h "$DB_HOST" -P "${DB_PORT:-3306}" -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" -e "
            SELECT 'Users' as Table_Name, COUNT(*) as Count FROM users
            UNION SELECT 'Teams', COUNT(*) FROM teams
            UNION SELECT 'Matches', COUNT(*) FROM matches
            UNION SELECT 'Venues', COUNT(*) FROM bars
            UNION SELECT 'Predictions', COUNT(*) FROM predictions
            UNION SELECT 'Animations', COUNT(*) FROM animations;
        "
    fi
}

# Boucle principale
while true; do
    show_menu
    read -p "Choisissez une option (1-7): " choice
    
    case $choice in
        1)
            BACKUP_FILE="$BACKUP_DIR/local_backup_$TIMESTAMP.sql"
            mkdir -p "$BACKUP_DIR"
            create_backup "local" "$BACKUP_FILE"
            ;;
        2)
            BACKUP_FILE="$BACKUP_DIR/production_backup_$TIMESTAMP.sql"
            mkdir -p "$BACKUP_DIR"
            create_backup "production" "$BACKUP_FILE"
            ;;
        3)
            sync_full
            ;;
        4)
            sync_safe
            ;;
        5)
            sync_data_only
            ;;
        6)
            compare_databases
            ;;
        7)
            log_info "Au revoir!"
            exit 0
            ;;
        *)
            log_error "Option invalide!"
            ;;
    esac
    
    echo ""
    read -p "Appuyez sur Entrée pour continuer..."
done

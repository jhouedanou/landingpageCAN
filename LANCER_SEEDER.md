# 🚀 Comment Lancer le Seeder

## ✅ Chips PDV Ajoutés!

Les chips colorés des PDV sont maintenant affichés dans la liste des matchs `/matches`!

Caractéristiques:
- 🏙️ Bleu = PDV Dakar
- 🗺️ Vert = PDV Régions
- 🍽️ Orange = CHR
- 🎉 Violet = Fanzone
- Affiche jusqu'à 10 PDV, avec lien "+X autres" vers la carte
- Nom du PDV + Zone affichés

---

## 🔧 LANCER LE SEEDER (Commandes)

### Option 1: Avec dev.sh (Recommandé)

```bash
# À la racine du projet
./dev.sh

# Puis dans le conteneur:
php artisan migrate:fresh
php artisan db:seed --class=FreshDeploymentSeeder
exit
```

### Option 2: Docker Exec Direct

```bash
# Méthode complète
docker exec -it landingpagecan-laravel.test-1 bash
cd /var/www/html
php artisan migrate:fresh
php artisan db:seed --class=FreshDeploymentSeeder
exit
```

### Option 3: Une seule commande

```bash
docker exec -it landingpagecan-laravel.test-1 bash -c "cd /var/www/html && php artisan migrate:fresh --force && php artisan db:seed --class=FreshDeploymentSeeder --force"
```

---

## 🧪 Vérifier Après le Seeder

```bash
docker exec -it landingpagecan-laravel.test-1 bash -c "cd /var/www/html && php artisan tinker --execute=\"echo 'Teams: ' . App\Models\Team::count() . PHP_EOL; App\Models\Team::pluck('name', 'iso_code')->each(fn(\\\$name, \\\$iso) => print('  ' . \\\$iso . ' => ' . \\\$name . PHP_EOL));\""
```

Ou manuellement:
```bash
docker exec -it landingpagecan-laravel.test-1 bash
cd /var/www/html
php artisan tinker

# Dans tinker:
>>> App\Models\Team::count()  // Doit être 8
>>> App\Models\Team::pluck('name', 'iso_code')
>>> App\Models\Bar::count()   // Nombre de PDV du CSV
>>> App\Models\MatchGame::count()  // Nombre de matchs
>>> App\Models\Animation::count()  // Nombre de liens match-PDV
```

---

## 📋 Résultat Attendu

Après le seeder, vous devriez avoir:

### 8 Équipes avec ISO codes:
- sn => SENEGAL
- bw => BOTSWANA
- za => AFRIQUE DU SUD
- eg => EGYPTE
- cd => RD CONGO
- ci => COTE D'IVOIRE
- cm => CAMEROUN
- bj => BENIN

### Tous les PDV du CSV:
- Environ 100+ PDV selon votre CSV
- Avec coordonnées GPS
- Avec type_pdv (dakar/regions/chr/fanzone)

### Tous les Matchs:
- Matchs de poules + Playoffs
- Avec dates et heures

### Animations (Liens Match-PDV):
- Chaque match lié aux PDV où il sera diffusé

---

## 🎨 Tester les Chips

1. Lancer le seeder (voir ci-dessus)
2. Aller sur `/matches`
3. Sous chaque match, vous verrez:
   - "📍 Diffusé dans X PDV"
   - Chips colorés avec emoji + nom du PDV + zone
   - Lien "+X autres" si plus de 10 PDV

---

## 🐛 En Cas de Problème

### Erreur "Could not open input file: artisan"
**Solution:** Utiliser `cd /var/www/html` avant les commandes artisan

### Erreur "No such container"
**Solution:** Lancer Docker d'abord
```bash
docker compose up -d
```

### Les PDV ne s'affichent pas
**Cause:** Pas d'animations dans la base
**Solution:**
1. Vérifier que le CSV est à la racine
2. Relancer le seeder complet

### Les drapeaux ne s'affichent pas
**Cause:** Pas d'iso_code sur les équipes
**Solution:**
```bash
docker exec -it landingpagecan-laravel.test-1 bash -c "cd /var/www/html && php artisan db:seed --class=TeamIsoCodesSeeder"
```

---

## 📝 Fichiers Modifiés

1. ✅ `resources/views/matches.blade.php` - Ajout des chips PDV (2 sections)
2. ✅ `app/Http/Controllers/Web/HomeController.php` - Eager loading des animations

---

## 🎯 Commande Rapide (Copy-Paste)

```bash
# Tout en une ligne
docker exec -it landingpagecan-laravel.test-1 bash -c "cd /var/www/html && php artisan migrate:fresh --force && php artisan db:seed --class=FreshDeploymentSeeder --force && php artisan cache:clear && php artisan view:clear"
```

Puis tester sur `/matches`!

---

**Date:** 19 Décembre 2025
**Status:** ✅ Chips PDV Implémentés
**Action requise:** Lancer le seeder avec une des commandes ci-dessus

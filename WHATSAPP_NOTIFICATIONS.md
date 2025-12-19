# Guide des Notifications WhatsApp - GAZELLE CAN 2025

## Vue d'ensemble

Le système de notifications WhatsApp est **déjà implémenté et fonctionnel**. Il envoie automatiquement des messages WhatsApp aux utilisateurs lorsqu'ils font des pronostics corrects.

## Fonctionnalités Implémentées

### 1. Notification pour Vainqueur Correct (+3 points)

Lorsqu'un utilisateur prédit correctement le vainqueur d'un match, il reçoit automatiquement un message WhatsApp:

```
🎉 *Bravo !*

Vous avez correctement prédit le vainqueur du match :
*Sénégal* 2 - 1 *Cameroun*

✅ +3 points gagnés !
📊 Total de vos points : 15
```

### 2. Notification pour Score Exact (+3 points bonus)

Lorsqu'un utilisateur prédit le score exact d'un match, il reçoit un message spécial:

```
🏆 *INCROYABLE !*

Vous avez prédit le score EXACT du match :
*Sénégal* 2 - 1 *Cameroun*

🎯 Score exact ! +3 points bonus !
📊 Total de vos points : 18
```

### 3. Confirmation de Pronostic

Lorsqu'un utilisateur enregistre un pronostic, il reçoit une confirmation:

```
🎯 *Pronostic enregistré !*

Sénégal 2 - 1 Cameroun

📅 23/12/2025 à 15:00
📍 Stade Abdoulaye Wade
🏆 Points potentiels : 1 pt + jusqu'à 6 pts bonus

Validé depuis : CHEZ JEAN
```

## Architecture Technique

### 1. Service WhatsApp

**Fichier:** `app/Services/WhatsAppService.php`

Le service utilise **Green API** pour envoyer des messages WhatsApp.

**Méthodes principales:**
- `sendMessage(string $phoneNumber, string $message)` - Envoie un message WhatsApp
- `sendPredictionConfirmation($user, $match, $prediction, $venue)` - Envoie la confirmation de pronostic
- `formatWhatsAppNumber(string $phone)` - Formate le numéro pour différents pays (CI, SN, FR)

### 2. Job de Traitement des Points

**Fichier:** `app/Jobs/ProcessMatchPoints.php`

Le job `ProcessMatchPoints` est déclenché automatiquement lorsqu'un match est terminé:

**Ligne 110-128:** Notification pour vainqueur correct
```php
if ($predictedWinner === $actualWinner) {
    // ... attribution des points ...

    $this->whatsAppService->sendMessage(
        $this->whatsAppService->formatWhatsAppNumber($prediction->user->phone),
        $message
    );
}
```

**Ligne 148-166:** Notification pour score exact
```php
if ($prediction->score_a == $match->score_a && $prediction->score_b == $match->score_b) {
    // ... attribution des points bonus ...

    $this->whatsAppService->sendMessage(
        $this->whatsAppService->formatWhatsAppNumber($prediction->user->phone),
        $message
    );
}
```

### 3. Gestion des Erreurs

Le système inclut une gestion robuste des erreurs:
- Logging détaillé dans `storage/logs/laravel.log`
- Try-catch pour éviter que les erreurs WhatsApp ne bloquent le traitement des points
- Retour d'information en cas d'échec d'envoi

## Configuration

### 1. Variables d'Environnement

Ajoutez ces variables dans votre fichier `.env`:

```env
GREENAPI_URL=https://api.green-api.com
GREENAPI_MEDIA_URL=https://media.green-api.com
GREENAPI_ID_INSTANCE=votre-instance-id
GREENAPI_API_TOKEN=votre-api-token
```

### 2. Obtenir les Identifiants Green API

1. Créez un compte sur [green-api.com](https://green-api.com)
2. Créez une nouvelle instance WhatsApp
3. Obtenez votre `ID Instance` et `API Token`
4. Scannez le QR code pour connecter votre numéro WhatsApp

### 3. Configuration du Fichier

Le fichier `config/services.php` est déjà configuré (lignes 38-43):

```php
'greenapi' => [
    'url' => env('GREENAPI_URL'),
    'media_url' => env('GREENAPI_MEDIA_URL'),
    'id_instance' => env('GREENAPI_ID_INSTANCE'),
    'api_token' => env('GREENAPI_API_TOKEN'),
],
```

## Format des Numéros de Téléphone

Le service supporte plusieurs formats de numéros:

### Côte d'Ivoire
- Format: `+225 XX XX XX XX XX` (13 chiffres)
- Exemple: `+225 01 02 03 04 05`

### Sénégal
- Format: `+221 XX XXX XX XX` (12 chiffres)
- Exemple: `+221 77 123 45 67`

### France
- Format: `+33 X XX XX XX XX` (11 chiffres)
- Exemple: `+33 6 12 34 56 78`

Le système nettoie automatiquement les espaces et caractères spéciaux.

## Déclenchement des Notifications

### Automatique

Les notifications sont envoyées automatiquement lorsque:

1. **Match terminé** - L'administrateur met à jour le statut du match à "finished"
2. **Scores finaux saisis** - Les scores `score_a` et `score_b` sont enregistrés
3. **Job exécuté** - Le job `ProcessMatchPoints` est lancé (automatiquement via le système de queue)

### Manuel (pour tests)

Vous pouvez déclencher manuellement le traitement:

```bash
php artisan tinker

# Traiter les points d'un match spécifique
\App\Jobs\ProcessMatchPoints::dispatch(MATCH_ID);

# Ou directement
$job = new \App\Jobs\ProcessMatchPoints(MATCH_ID);
$job->handle();
```

## Logs et Débogage

### Vérifier les Logs

Les logs sont dans `storage/logs/laravel.log`:

```bash
# Voir les logs WhatsApp
tail -f storage/logs/laravel.log | grep -i whatsapp

# Ou dans Docker
docker exec landingpagecan-laravel.test-1 tail -f storage/logs/laravel.log | grep -i whatsapp
```

### Informations Loggées

- Configuration Green API (ligne 21-25)
- URL de l'API (ligne 38)
- Payload envoyé (ligne 45)
- Réponse reçue (ligne 52-56)
- Erreurs éventuelles (ligne 63-73)

### Exemple de Log Réussi

```
[INFO] === DEBUT sendWhatsAppMessage ===
[INFO] Configuration Green API {"id_instance":"123456","api_token":"abc123..."}
[INFO] URL Green API {"url":"https://api.green-api.com/waInstance123456/sendMessage/abc123..."}
[INFO] Payload WhatsApp {"chatId":"221771234567@c.us","message":"🎉 *Bravo !*..."}
[INFO] Envoi requete HTTP vers Green API...
[INFO] Reponse Green API recue {"status":200,"body":"..."}
[INFO] === SUCCES WhatsApp ===
```

## Tests

### Test d'Envoi Simple

```bash
php artisan tinker
```

```php
$service = new \App\Services\WhatsAppService();
$phone = '221771234567'; // Votre numéro de test
$message = "🧪 Test de notification GAZELLE";
$result = $service->sendMessage($phone, $message);
print_r($result);
```

### Test de Notification Complète

```php
// Trouver un match terminé
$match = \App\Models\MatchGame::where('status', 'finished')->first();

// Déclencher le job
\App\Jobs\ProcessMatchPoints::dispatch($match->id);

// Vérifier les logs
// Les utilisateurs ayant fait des bons pronostics recevront un message
```

## Règles de Points

Les notifications sont envoyées selon ces règles:

| Type de Pronostic | Points | Notification |
|-------------------|---------|--------------|
| Participation | +1 pt | Non (pas de notification) |
| Vainqueur correct | +3 pts | Oui (🎉 Bravo !) |
| Score exact | +3 pts bonus | Oui (🏆 INCROYABLE !) |
| **Total maximum** | **7 pts** | Jusqu'à 2 messages par match |

**Important:**
- Un utilisateur peut recevoir 2 notifications pour le même match (vainqueur + score exact)
- Les points de participation (+1 pt) ne déclenchent pas de notification
- Les notifications incluent le total des points de l'utilisateur

## Dépannage

### Problème: Aucune notification envoyée

**Solutions:**

1. Vérifier la configuration `.env`:
   ```bash
   php artisan config:clear
   php artisan config:cache
   ```

2. Vérifier que les variables sont bien chargées:
   ```bash
   php artisan tinker
   config('services.greenapi.id_instance')
   ```

3. Vérifier que le job a bien été exécuté:
   ```bash
   # Voir les jobs en queue
   SELECT * FROM jobs ORDER BY id DESC LIMIT 10;

   # Voir les jobs échoués
   SELECT * FROM failed_jobs ORDER BY id DESC LIMIT 10;
   ```

### Problème: Configuration Green API incomplete

**Erreur dans les logs:**
```
[ERROR] Configuration Green API incomplete !
```

**Solution:** Assurez-vous que toutes les variables sont définies dans `.env`

### Problème: HTTP 403 ou 401

**Erreur:**
```
[ERROR] === ECHEC WhatsApp === {"status":403,"body":"..."}
```

**Solutions:**
- Vérifiez que votre API Token est correct
- Vérifiez que votre instance Green API est active
- Rescannez le QR code sur le dashboard Green API

### Problème: Numéro invalide

**Erreur:**
```
[ERROR] Invalid phone number format
```

**Solution:**
- Vérifiez que le numéro dans la base de données est complet avec l'indicatif pays
- Format attendu: `221XXXXXXXXX` (sans espaces ni caractères spéciaux)

## Commandes Utiles

```bash
# Vider la queue de jobs
php artisan queue:clear

# Traiter les jobs en attente
php artisan queue:work --once

# Voir les jobs échoués
php artisan queue:failed

# Réessayer un job échoué
php artisan queue:retry JOB_ID

# Nettoyer les logs
truncate -s 0 storage/logs/laravel.log

# Test de configuration
php artisan tinker
>>> config('services.greenapi')
```

## Bonnes Pratiques

1. **Testez d'abord en local** avec un seul numéro avant le déploiement
2. **Surveillez les logs** pendant les premières heures après déploiement
3. **Limitez les tests** - Green API a des quotas
4. **Utilisez la queue** - Ne bloquez jamais les requêtes utilisateur
5. **Gérez les erreurs gracieusement** - Les notifications sont un bonus, pas un bloquant

## Support Green API

- Documentation: https://green-api.com/docs/
- Dashboard: https://console.green-api.com/
- Support: support@green-api.com

---

**Dernière mise à jour:** 19 Décembre 2025
**Version:** 1.0
**Status:** ✅ Implémenté et Fonctionnel

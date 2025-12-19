$CREATE_RELEASE()

cd $FORGE_RELEASE_DIRECTORY

$FORGE_COMPOSER install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# --- DEBUT AJOUT FRONTEND ---
npm ci
npm run build
# --- FIN AJOUT FRONTEND ---

$FORGE_PHP artisan migrate --force --seed
$FORGE_PHP artisan optimize
$FORGE_PHP artisan storage:link

$ACTIVATE_RELEASE()

$RESTART_QUEUES()
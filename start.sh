#!/bin/bash
set -e

# Lien de stockage public
php artisan storage:link --force 2>/dev/null || true

# Cache de production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Migrations
php artisan migrate --force

# Démarrage du serveur
php artisan serve --host=0.0.0.0 --port=${PORT:-8000}

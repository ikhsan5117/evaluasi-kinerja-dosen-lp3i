#!/bin/sh
set -e

echo "🚀 Starting LP3I Evaluasi Kinerja Dosen container..."

# Create storage directories if missing
mkdir -p /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/framework/cache \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache

# Fix permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Generate key if empty
if [ -z "$APP_KEY" ]; then
    echo "🔑 Generating APP_KEY..."
    php artisan key:generate --force
fi

# Run migrations and seeders if database configured
echo "📦 Running database migrations..."
php artisan migrate --force --isolated

# Auto-seed if database is fresh (no users yet)
USER_COUNT=$(php artisan tinker --execute="echo App\Models\User::count();" 2>/dev/null || echo "0")
if [ "$USER_COUNT" = "0" ]; then
    echo "🌱 Seeding initial data (Admin, Dosen, Mahasiswa, Matkul)..."
    php artisan db:seed --force
fi

# Cache configurations for maximum production performance
echo "⚡ Caching configurations & routes..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ App ready! Starting Nginx & PHP-FPM..."
exec /usr/bin/supervisord -c /etc/supervisord.conf

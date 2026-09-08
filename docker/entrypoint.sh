#!/bin/sh
set -e

echo "[*] Starting Laravel Application Container..."

# Background sync for Cloudflare Worker tunnel URL
(
    sleep 6
    php artisan tunnel:sync || true
) &

# Start Laravel server (Docker will auto-restart if it exits)
echo "[*] Laravel server listening on http://0.0.0.0:8005"
exec php artisan serve --host=0.0.0.0 --port=8005

#!/bin/bash
set -e

echo "Waiting for database..."
until /usr/bin/php /var/www/html/main_server/artisan db:show >/dev/null 2>&1; do
  sleep .2
done
echo "connected"

/usr/bin/php /var/www/html/main_server/artisan migrate --force
/usr/bin/php /var/www/html/main_server/artisan settings:sync --force

# Uncomment and comment out above code to test with auto-config.inc.php in docker
#/bin/bash /tmp/write-config.sh

apachectl -D FOREGROUND

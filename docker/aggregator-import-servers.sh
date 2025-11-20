#!/bin/bash
set -e

/usr/bin/php /var/www/html/main_server/artisan settings:sync --force
/usr/bin/php /var/www/html/main_server/artisan aggregator:ImportRootServers

FROM bmltenabled/bmlt-server-base:8.2

COPY build/bmlt-server.zip /tmp/bmlt-server.zip
RUN \
  echo '<html><head><meta http-equiv="refresh" content="0; URL=main_server/" /></head><body>OK</body></html>' \
    >> /var/www/html/index.html \
  && unzip /tmp/bmlt-server.zip -d /var/www/html \
  && rm -f /tmp/bmlt-server.zip \
  && chown -R www-data: /var/www/html

# Handle mod_dir redirect using X-Forwarded headers
COPY docker/.htaccess /var/www/html/main_server/.htaccess

COPY docker/write-config.sh /tmp/write-config.sh
COPY docker/start-bmlt.sh /tmp/start-bmlt.sh
COPY docker/aggregator-initialize-database.sh /tmp/aggregator-initialize-database.sh
COPY docker/aggregator-import-servers.sh /tmp/aggregator-import-servers.sh

CMD ["/bin/bash", "/tmp/start-bmlt.sh"]

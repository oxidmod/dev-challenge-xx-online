FROM openswoole/swoole:22.0-php8.2

# Install extensions
RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
  && docker-php-ext-install pdo pdo_pgsql pgsql \

FROM php:7.4.1-fpm

WORKDIR /var/www/html/intro-app

COPY install-composer.sh /
RUN apt-get update \
  && apt-get install -y wget git unzip libpq-dev libfreetype6-dev libjpeg62-turbo-dev libpng-dev \
  && : 'Install Node.js' \
  &&  curl -sL https://deb.nodesource.com/setup_12.x | bash - \
  && apt-get install -y nodejs \
  && : 'Install PHP Extensions' \
  && docker-php-ext-install -j$(nproc) pdo_pgsql \
  && : 'Install Composer' \
  && chmod 755 /install-composer.sh \
  && /install-composer.sh \

COPY . .

WORKDIR /var/www/html/intro-app/web

CMD ["php","artisan", "serve", "--host", "0.0.0.0", "--port", "8085"]

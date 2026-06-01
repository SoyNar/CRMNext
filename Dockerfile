#config docker
FROM php:8.2-apache

# Instalar dependencias
RUN apt-get update && apt-get install -y \
    nodejs npm git curl zip unzip \
    libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql opcache gd \
    && a2enmod rewrite

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts

COPY package.json package-lock.json ./
RUN npm ci

COPY . .

RUN npm run build
RUN composer run-script post-autoload-dump || true
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache


# Apache config para Laravel
RUN echo '<VirtualHost *:10000>\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

RUN sed -i 's/Listen 80/Listen 10000/' /etc/apache2/ports.conf

EXPOSE 10000

CMD ["sh", "-c", "php artisan config:cache && php artisan migrate --force && php artisan db:seed --force && apache2-foreground"]

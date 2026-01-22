FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    git unzip libicu-dev libzip-dev zip curl \
    && docker-php-ext-install intl pdo pdo_mysql zip \
    && a2enmod rewrite

WORKDIR /var/www/html

# Copier uniquement composer d'abord (meilleur cache)
COPY composer.json composer.lock symfony.lock* ./

RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# Copier le reste du projet
COPY . .

# DocumentRoot = /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf \
 && sed -ri 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Permissions
RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 755 /var/www/html

EXPOSE 80

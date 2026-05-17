FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libssl-dev \
    libzip-dev \
    zip \
    pkg-config \
    git \
    curl \
    build-essential \
    autoconf \
    && rm -rf /var/lib/apt/lists/*

# Pin ext-mongodb PECL to 1.x — ext-mongodb 2.x (released 2024) changed the
# bsonSerialize() interface signature, making it incompatible with the
# mongodb/mongodb PHP library 1.x. Pin to 1.21.1 to keep both layers aligned.
RUN pecl install mongodb-1.21.1 \
    && docker-php-ext-enable mongodb \
    && docker-php-ext-install zip

RUN a2enmod rewrite

# Pass Docker env vars (MONGODB_URL, VIRTUSTOTAL_API) through to PHP via Apache
COPY passenv.conf /etc/apache2/conf-enabled/passenv.conf

WORKDIR /var/www/html

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY composer.json composer.lock* ./

RUN composer install --no-interaction --prefer-dist --no-dev --no-scripts --ignore-platform-reqs

COPY --chown=www-data:www-data . .

# On Linux (case-sensitive) dbController.php and dbcontroller.php are two
# separate files. Delete the old capital-C copy so PHP doesn't load it twice.
RUN rm -f /var/www/html/Controller/dbController.php


RUN composer run-script post-install-cmd --no-interaction 2>/dev/null || true

EXPOSE 80
CMD ["apache2-foreground"]
FROM php:8.2-cli

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
    libzip-dev \
    libonig-dev \
    libicu-dev \
    libxml2-dev \
    libsqlite3-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    nodejs \
    npm \
  && docker-php-ext-install pdo pdo_sqlite mbstring pcntl bcmath xml zip intl gd \
  && apt-get clean \
  && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY composer.json composer.lock artisan bootstrap/app.php ./
RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction

COPY package*.json ./
RUN npm ci

COPY . .
RUN npm run build
RUN php -r "file_exists('.env') || copy('.env.example', '.env');"
RUN php artisan key:generate --force

EXPOSE 10000
CMD ["bash", "-lc", "php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=${PORT:-10000}"]

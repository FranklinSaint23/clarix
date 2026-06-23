FROM php:8.3-cli

# Extensions système
RUN apt-get update && apt-get install -y \
    git curl zip unzip \
    libpng-dev libonig-dev libxml2-dev libzip-dev \
    ca-certificates \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Node.js 20
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

# Build production
RUN composer install --no-dev --optimize-autoloader --no-interaction
RUN npm ci && npm run build && rm -rf node_modules

RUN chmod +x start.sh \
    && mkdir -p storage/framework/sessions \
    && mkdir -p storage/framework/views \
    && mkdir -p storage/framework/cache/data \
    && mkdir -p storage/logs \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 8000

CMD ["./start.sh"]

FROM php:8.2-fpm-alpine AS base

# System deps
RUN apk add --no-cache \
    bash \
    icu-dev \
    libxml2-dev \
    postgresql-dev \
    libsodium \
    oniguruma-dev \
    unzip \
    git

# PHP extensions
RUN docker-php-ext-install \
    intl \
    pdo \
    pdo_pgsql \
    xml \
    opcache

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app


# ========================
# BUILD STAGE
# ========================
FROM base AS build

ENV APP_ENV=prod
ENV APP_DEBUG=0

COPY . .

RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
 && composer dump-autoload --classmap-authoritative


# ========================
# RUNTIME STAGE
# ========================
FROM base AS runtime

WORKDIR /app

COPY --from=build /app /app

RUN addgroup -g 1000 app \
 && adduser -G app -g app -s /bin/sh -D app \
 && chown -R app:app /app

USER app

CMD ["php-fpm"]
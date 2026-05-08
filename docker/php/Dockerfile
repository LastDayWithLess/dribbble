FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libpq-dev \
    libcurl4-openssl-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install \
    pdo_pgsql \
    mbstring \
    exif \
    pcntl \
    gd \
    zip \
    opcache \
    curl

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN addgroup --system --gid 1000 appuser && \
    adduser --system --uid 1000 --gid 1000 appuser

WORKDIR /var/www/symfony

RUN echo "memory_limit = 300M" > /usr/local/etc/php/conf.d/memory-limit.ini && \
    echo "upload_max_filesize = 3M" > /usr/local/etc/php/conf.d/upload-size.ini && \
    echo "post_max_size = 3M" > /usr/local/etc/php/conf.d/post-size.ini && \
    echo "max_execution_time = 200" > /usr/local/etc/php/conf.d/timeout.ini

RUN echo "opcache.memory_consumption=150" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.interned_strings_buffer=10" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.max_accelerated_files=5000" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.revalidate_freq=2" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.fast_shutdown=1" >> /usr/local/etc/php/conf.d/opcache.ini

RUN echo "date.timezone = UTC" > /usr/local/etc/php/conf.d/timezone.ini

RUN chown -R appuser:appuser /var/www/symfony
USER appuser
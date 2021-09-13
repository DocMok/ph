FROM php:8.0-fpm

RUN apt update && apt install -y \
        cron \
        nano \
        git \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libonig-dev \
        libxml2-dev \
        zlib1g-dev \
        libzip-dev \
    && docker-php-ext-install -j$(nproc) iconv \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-configure gd \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install zip \
    && docker-php-ext-install exif

#RUN curl --silent --show-error https://getcomposer.org/installer | php && \
#    mv composer.phar /usr/local/bin/composer

RUN chown -R www-data:www-data /var/www/html
RUN mkdir /var/www/html/storage
RUN chmod -R 777 /var/www/html/storage





COPY crontab /etc/cron.d/crontab

# Give execution rights on the cron job
RUN chmod 0644 /etc/cron.d/crontab

# Apply cron job
RUN crontab /etc/cron.d/crontab
# Create the log file to be able to run tail
RUN touch /var/log/cron.log

# Run the command on container startup
CMD cron && tail -f /var/log/cron.log




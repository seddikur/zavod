FROM php:8.0.6-apache

#Install php-extentions
RUN apt-get update && apt-get install -y \
    zlib1g-dev \
    libzip-dev \
    unzip

RUN ["docker-php-ext-install", "zip"]
RUN ["docker-php-ext-install", "mysqli", "pdo", "pdo_mysql"]
RUN ["docker-php-ext-install", "opcache"]

RUN pecl install -o -f xdebug-3.1.5 \
    && docker-php-ext-enable xdebug

# install the PHP extensions
RUN set -ex; \
	\
	savedAptMark="$(apt-mark showmanual)"; \
	\
	apt-get update; \
	apt-get install -y --no-install-recommends \
		libfreetype6-dev \
		libjpeg-dev \
		libmagickwand-dev \
		libpng-dev \
	; \
	\
	docker-php-ext-configure gd --with-freetype --with-jpeg; \
	docker-php-ext-install -j "$(nproc)" \
		bcmath \
		exif \
		gd \
		mysqli \
		zip \
	; \
#	pecl install imagick-3.4.4; \
#	docker-php-ext-enable imagick; \
# install imagick
# use github version for now until release from https://pecl.php.net/get/imagick is ready for PHP 8
mkdir -p /usr/src/php/ext/imagick; \
curl -fsSL https://github.com/Imagick/imagick/archive/06116aa24b76edaf6b1693198f79e6c295eda8a9.tar.gz | tar xvz -C "/usr/src/php/ext/imagick" --strip 1; \
docker-php-ext-install imagick; \
	\
# reset apt-mark's "manual" list so that "purge --auto-remove" will remove all build dependencies
	apt-mark auto '.*' > /dev/null; \
	apt-mark manual $savedAptMark; \
	ldd "$(php -r 'echo ini_get("extension_dir");')"/*.so \
		| awk '/=>/ { print $3 }' \
		| sort -u \
		| xargs -r dpkg-query -S \
		| cut -d: -f1 \
		| sort -u \
		| xargs -rt apt-mark manual; \
	\
	apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false; \
	rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Add php-ini
ADD php.ini /usr/local/etc/php/conf.d/40-custom.ini

# Copy php.ini
#COPY ./php.ini /usr/local/etc/php/

# Set workdir
WORKDIR /var/www/html/

#The main purpose of a CMD is to provide defaults for an executing container. These defaults can include an executable,
# or they can omit the executable, in which case you must specify an ENTRYPOINT instruction as well.
CMD ["apache2-foreground"]
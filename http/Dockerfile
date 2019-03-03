# Dockerfile for sheldon. This is used when building a new image.

FROM php:7-apache

# Copy site files to container. If these files are changed or
# added to, will need to rebuild the image.
COPY html/ /var/www/html/

# Following could be used if we want to change Apache config.
# COPY config/apache2.conf /etc/apache2/apache2.conf
# COPY config/envvars /etc/apache2/envvars

# Use the default development php.ini configuration.
# Replace 'development' with 'production' for production environment.
RUN cp "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# Set the necessary permissions on the web files.
RUN chown -R www-data:www-data /var/www/html/*

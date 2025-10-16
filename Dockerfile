# Minimal example for running PHP + Apache + ODBC extensions.
# NOTE: IBM i Access ODBC package is NOT included here for licensing reasons.
# You must provide it separately and follow IBM's license terms.
FROM php:8.2-apache

# Core deps
RUN apt-get update && apt-get install -y --no-install-recommends     unixodbc odbcinst alien wget ca-certificates     && rm -rf /var/lib/apt/lists/*

# Example: install PHP PDO ODBC extension (unixODBC)
RUN docker-php-ext-install pdo_odbc

# Provide DSN file (mount or copy your own in production)
COPY odbc.ini /etc/odbc.ini

# Place a simple PHP info/ODBC test
COPY public/ /var/www/html/

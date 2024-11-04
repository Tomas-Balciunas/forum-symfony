FROM phpstorm/php-apache:8.2-xdebug3.2

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN apt-get update && apt-get -y install libzip-dev libicu-dev
RUN docker-php-ext-install pdo pdo_mysql zip mysqli
RUN docker-php-ext-enable pdo pdo_mysql zip mysqli

RUN a2enmod rewrite

COPY apache.conf /etc/apache2/sites-available/000-default.conf

COPY . /var/www/html
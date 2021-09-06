FROM ubuntu:latest

SHELL ["/bin/bash", "-c"]

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get -y update && apt-get -y install python python3 python3-pip nginx php-fpm php-sqlite3 gnupg supervisor php-pear php-dev composer

RUN mkdir /logs
RUN mkdir -p /run/php/

RUN ln -sf /dev/stdout /var/log/nginx/access.log \
    && ln -sf /dev/stderr /var/log/nginx/error.log \
    && ln -sf /dev/stdout /var/log/php.fmp.log

COPY files/composer.lock /var/www/composer.lock
COPY files/composer.json /var/www/composer.json
RUN cd /var/www && php composer.json install > /dev/null

COPY supervisord-config /etc/supervisor/conf.d/default.conf
COPY nginx-config /etc/nginx/sites-available/default

COPY files/ /var/www/
RUN cd /var/www/static/vendor/ && python3 initDB.py
RUN chown -R www-data:www-data /var/www/
RUN chmod 777 /var/www/static/vendor/db.sqlite3

RUN service nginx restart

ENTRYPOINT /usr/bin/supervisord
WORKDIR /var/www/

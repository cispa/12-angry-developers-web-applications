FROM ubuntu:latest

SHELL ["/bin/bash", "-c"]

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get -y update && apt-get -y install apt-utils curl python nginx supervisor build-essential
RUN curl -sL https://deb.nodesource.com/setup_10.x | /bin/bash > /dev/null 2>&1
RUN apt-get -y update && apt-get -y install nodejs

RUN mkdir -p /run/php/
RUN mkdir /logs

RUN ln -sf /dev/stdout /var/log/nginx/access.log \
    && ln -sf /dev/stderr /var/log/nginx/error.log

COPY files/package-lock.json /app/package-lock.json
COPY files/package.json /app/package.json
RUN cd /app && npm install > /dev/null 2>&1

COPY supervisord-config /etc/supervisor/conf.d/default.conf
COPY nginx-config /etc/nginx/sites-available/default

COPY files/ /app/
RUN cd /app/static/vendor/ && python3 initDB.py
RUN chmod 777 /app/static/vendor/db.sqlite3
RUN chmod -R 777 /app/static/

RUN service nginx restart

ENTRYPOINT /usr/bin/supervisord
WORKDIR /app/

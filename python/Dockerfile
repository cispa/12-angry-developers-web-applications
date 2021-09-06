FROM ubuntu:latest

SHELL ["/bin/bash", "-c"]

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get -y update
RUN apt-get -y install screen nginx supervisor python3 python3-pip uwsgi
RUN pip3 install Django uwsgi
RUN apt-get install uwsgi-plugin-python3

RUN mkdir -p /run/uwsgi/app/app
RUN mkdir /logs

RUN ln -sf /dev/stdout /var/log/nginx/access.log \
    && ln -sf /dev/stderr /var/log/nginx/error.log \
    && ln -sf /dev/stderr /var/log/uwsgi/uwsgi.log

COPY supervisord-config /etc/supervisor/conf.d/default.conf
COPY uwsgi_params /etc/nginx/uwsgi_params
COPY app.ini /etc/uwsgi/apps-enabled/app.ini
COPY nginx-config /etc/nginx/sites-available/default

COPY files/ /app

RUN cd /app && python3 manage.py makemigrations && python3 manage.py makemigrations app && python3 manage.py migrate

RUN service nginx restart

ENTRYPOINT /usr/bin/supervisord
WORKDIR /app/

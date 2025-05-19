FROM php:8.3.21-zts-alpine3.20

RUN apk update
RUN apk add bash
RUN apk add composer
RUN apk add php83-fileinfo

RUN docker-php-ext-install fileinfo

RUN php -m | grep fileinfo

ADD . /opt/sftp_php

RUN composer update --working-dir=/opt/sftp_php
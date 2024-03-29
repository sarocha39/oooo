FROM php:7.4-apache

RUN apt-get update &&\
    apt-get install -y libssh2-1-dev libssh2-1 &&\
    rm -rf /var/lib/apt/lists/*
RUN pecl install ssh2-1.3.1 &&\
    docker-php-ext-enable ssh2
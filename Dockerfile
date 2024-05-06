FROM php:apache

RUN apt-get update && apt-get install -y openssh-server sshpass libssh2-1-dev libssh2-1
RUN pecl install ssh2-1.3.1 && docker-php-ext-enable ssh2

RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

WORKDIR /var/www/html/

EXPOSE 80

CMD service ssh start && apache2-foreground
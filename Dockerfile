FROM php:apache

RUN apt-get update && apt-get install -y openssh-server sshpass python3 python3-pip libssh2-1-dev libssh2-1
RUN pecl install ssh2-1.3.1 && docker-php-ext-enable ssh2

RUN pip3 install paramiko --break-system-packages
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

RUN chown -R www-data:www-data /var/www

WORKDIR /var/www/html/

EXPOSE 80 22

CMD service ssh start && apache2-foreground
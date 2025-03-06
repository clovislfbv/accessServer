FROM php:apache

RUN apt-get update && apt-get install -y openssh-server sshpass libssh2-1-dev libssh2-1
RUN pecl install ssh2-1.3.1 && docker-php-ext-enable ssh2

RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

RUN echo "upload_max_filesize = 1400000000M" > /usr/local/etc/php/conf.d/upload.ini
RUN echo "post_max_size = 1400000000M" >> /usr/local/etc/php/conf.d/upload.ini

RUN apt-get update && apt-get install -y certbot python3-certbot-apache

WORKDIR /var/www/html/

EXPOSE ${port} ${port2}

CMD service ssh start && apache2-foreground
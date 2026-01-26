FROM php:apache

RUN apt-get update && apt-get install -y python3 python3-pip openssh-server sshpass libssh2-1-dev libssh2-1
RUN pecl install ssh2-1.3.1 && docker-php-ext-enable ssh2

COPY python/requirements.txt python/requirements.txt

RUN pip3 install -r python/requirements.txt --break-system-packages

ENV TZ=Europe/Paris
RUN echo "Europe/Paris" > /etc/timezone && \
    dpkg-reconfigure -f noninteractive tzdata

RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

RUN echo "upload_max_filesize = 1400000000M" > /usr/local/etc/php/conf.d/upload.ini
RUN echo "post_max_size = 1400000000M" >> /usr/local/etc/php/conf.d/upload.ini

RUN apt-get update && apt-get install -y certbot python3-certbot-apache

RUN a2enmod ssl

COPY 000-default.conf /etc/apache2/sites-available/000-default.conf
COPY 000-default-ssl.conf /etc/apache2/sites-available/000-default-ssl.conf

RUN a2ensite 000-default-ssl

WORKDIR /var/www/html/

EXPOSE ${port} ${port2}

CMD service ssh start && apache2-foreground
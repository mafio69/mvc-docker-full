FROM php:7.3-apache-stretch

MAINTAINER tobias@proudcommerce.com

ENV HOME /root
RUN echo 'debconf debconf/frontend select Noninteractive' apt-get -yq install apt-utils | debconf-set-selections

# timezone / date
RUN echo "Europe/Warsaw" > /etc/timezone&& dpkg-reconfigure -f noninteractive tzdata

# install packages
RUN apt-get update -y && \
  apt-get install -y --no-install-recommends \
  less vim wget unzip rsync git mysql-client postfix autossh \
  libcurl4-openssl-dev libfreetype6 libjpeg62-turbo libpng-dev libjpeg-dev libxml2-dev libc-client-dev libkrb5-dev libzip-dev libwebp6 libxpm4 && \
  apt -y install dialog &&\
  apt-get clean && \
  apt-get autoremove -y && \
  rm -rf /var/lib/apt/lists/* && \
  echo "export TERM=xterm" >> /root/.bashrc

RUN curl -sL https://deb.nodesource.com/setup_10.x | bash -
# install php extensions
RUN docker-php-ext-configure gd --with-jpeg-dir=/usr/local/ && \
  docker-php-ext-configure imap --with-kerberos --with-imap-ssl && \
  docker-php-ext-install -j$(nproc) curl json xml mbstring zip bcmath soap pdo_mysql mysqli gd gettext imap && \
  docker-php-ext-install mysqli

# install ioncube
RUN curl -o ioncube.tar.gz http://downloads3.ioncube.com/loader_downloads/ioncube_loaders_lin_x86-64.tar.gz \
    && tar -xvvzf ioncube.tar.gz \
    && mv ioncube/ioncube_loader_lin_7.3.so `php-config --extension-dir` \
    && rm -Rf ioncube.tar.gz ioncube \
    && docker-php-ext-enable ioncube_loader_lin_7.3

# composer stuff
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN chown www-data:www-data /var/www/html

# install mod_pagespeed
RUN wget https://dl-ssl.google.com/dl/linux/direct/mod-pagespeed-stable_current_amd64.deb
RUN dpkg -i mod-pagespeed-*.deb
RUN rm mod-pagespeed-*.deb
RUN sed -i -e 's/ModPagespeed on/ModPagespeed off/g' /etc/apache2/mods-available/pagespeed.conf

# apache stuff
RUN /usr/sbin/a2enmod rewrite && /usr/sbin/a2enmod headers && /usr/sbin/a2enmod expires && /usr/sbin/a2enmod pagespeed
COPY ./docker/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY ./docker/apache2.conf /etc/apache2/apache2.conf

RUN pecl install xdebug-3.0.0
RUN docker-php-ext-enable xdebug \
    && sed -i '1 a xdebug.remote_enable=1' /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && sed -i '1 a xdebug.remote_connect_back=1' /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN chmod -R 765 /var/www/html

RUN mkdir -p -m 0777 /var/www/html/App
RUN mkdir -p -m 0777 /var/www/html/Public

COPY ./docker/php.ini /usr/local/etc/php/

EXPOSE 80
FROM php:7.0-apache

RUN apt-get update
RUN apt-get install -y software-properties-common
RUN apt-get install -y wget curl git gearman libgearman-dev re2c sudo supervisor unzip zip

# install composer
RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/bin/composer

# Install from source
RUN mkdir -p /tmp/install
WORKDIR /tmp/install
RUN wget https://github.com/wcgallego/pecl-gearman/archive/master.zip
RUN unzip master.zip
WORKDIR /tmp/install/pecl-gearman-master
RUN phpize
RUN ./configure
RUN make install
RUN echo "extension=gearman.so" > /usr/local/etc/php/php.ini
RUN rm -rf /tmp/install/pecl-gearman-master
RUN rm /tmp/install/master.zip

# Verify if module is really installed
RUN php -m | grep gearman

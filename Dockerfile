FROM php:7.0-apache

ARG GITHUB_TOKEN=
ENV GITHUB_TOKEN ${GITHUB_TOKEN}

RUN apt-get update

# install curl and get
RUN apt-get install -y curl git unzip zip

# install composer
RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/bin/composer

# add application source
COPY . /var/www/html

# set github token
RUN composer config --global github-oauth.github.com $GITHUB_TOKEN

# install the composer deps
RUN cd /var/www/html && composer update --no-plugins --no-scripts

# generate latest files
RUN cd /var/www/html && ./vendor/bin/satis --no-interaction build satis.json ./generated --skip-errors

# change permissions apache can write to directories
RUN usermod -u 1000 www-data
RUN chown -R www-data:www-data /var/www/html/logs
RUN chown -R www-data:www-data /var/www/html/generated

# Expose port 80
EXPOSE 80

# run apache
CMD /usr/sbin/apache2ctl -D FOREGROUND

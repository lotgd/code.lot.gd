FROM php7-gearman

ARG GITHUB_TOKEN=
ENV GITHUB_TOKEN ${GITHUB_TOKEN}
ENV COMPOSER_HOME /var/www/html

# add application source
COPY . /var/www/html
WORKDIR /var/www/html

# change permissions apache can write to directories
RUN usermod -u 1000 www-data
RUN chown -R www-data:www-data /var/www/html

# set github token
RUN sudo -u www-data -E -- composer config --global github-oauth.github.com ${GITHUB_TOKEN}

# install the composer deps
RUN sudo -u www-data -E -- composer update --no-plugins --no-scripts

# generate latest files
RUN sudo -u www-data -E -- php -r 'require "regenerate.php"; regenerate_fn(new GearmanJob());'

# copy supervisor conf files
COPY conf.d/* /etc/supervisor/conf.d/

# Expose port 80
EXPOSE 80

# run services, including apache, to start the app
CMD ["/usr/bin/supervisord", "-n"]

FROM php:8.3-fpm


# mix
RUN apt-get update \
  && apt-get install  -y curl git

# Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /composer
ENV PATH $PATH:/composer/vendor/bin
RUN composer config --global process-timeout 3600


WORKDIR /root
RUN git clone https://github.com/seebi/dircolors-solarized

EXPOSE 5173
WORKDIR /var/www/html 

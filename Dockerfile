ARG PHP_VERSION

FROM php:${PHP_VERSION}-cli

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN export DEBIAN_FRONTEND=noninteractive \
    && apt-get update && apt-get -y upgrade \
    && apt-get -y --no-install-recommends install apt-utils unzip git

RUN chmod uga+x /usr/local/bin/install-php-extensions && sync && install-php-extensions zip pcntl

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');" \
    && mv composer.phar /usr/bin/composer

EXPOSE 80

CMD ["php", "/code/testapp.php", "react:start-server", "--workerAmount=10"]
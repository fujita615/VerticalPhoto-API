FROM  php:8.3-fpm

#dockerでパッケージ管理ツールaptが使えるようupdate
RUN apt update -y \
#続けてaptを使って諸々モジュールをインストール
  && apt install -y  tar zip unzip vim wget cron rsyslog curl libfreetype6-dev libjpeg62-turbo-dev libpng-dev
RUN apt-get update \
  && apt-get install -y locales libz-dev
RUN localedef -f UTF-8 -i ja_JP ja_JP.UTF-8

#timezoneを日本に設定
ENV TZ=Asia/Tokyo \
# localeを日本用に設定
LANG=ja_JP.UTF-8 \
LANGUAGE=ja_JP:ja \
LC_ALL=ja_JP.UTF-8

#laravelに必要なphpモジュールをインストール
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install pdo_mysql bcmath gd
RUN pecl install redis && \  
docker-php-ext-enable redis

#composerをインストール
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER 1

COPY ./docker/php/php.ini /usr/local/etc/php/php.ini

WORKDIR /usr/local/apache2/htdocs
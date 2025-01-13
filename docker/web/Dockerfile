FROM amazonlinux:2023.3.20240131.0
#timezoneを日本に設定
ENV TZ=Asia/Tokyo \
# localeを日本用に設定
LANG=ja_JP.UTF-8 \
LANGUAGE=ja_JP:ja \
LC_ALL=ja_JP.UTF-8

#apacheや日本語用モジュールなどをインストール
RUN dnf update -y \
  && dnf install -y \
  glibc-langpack-ja \
  tar \
  zip \
  unzip \
  vim \
  wget \
  cronie \
  rsyslog \
  httpd


#phpライブラリをインストールする
RUN dnf clean metadata \
  && dnf install -y \
  php-fpm \
  php-mysqli \
  php-json \
  php \
  php-gd \
  php-devel

# Node.jsをインストール
COPY --from=node:20.11.1 /usr/local/bin /usr/local/bin
# npmをインストール
COPY --from=node:20.11.1 /usr/local/lib /usr/local/lib
# yarnをインストール
COPY --from=node:20.11.1 /opt /opt

# composerをインストール
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER 1

#php.iniを編集してコピーする
COPY ./docker/web/php.ini /etc/php.ini
#apachのvirtualhostとmod-rewriteファイルをlaravelで使えるよう編集してコピー
COPY ./docker/apache/vhost.conf /etc/httpd/conf.d/vhost.conf
COPY ./docker/apache/rewrite.conf /etc/httpd/conf.modules.d/rewrite.conf
#php-fpmとapacheを連動させるようconfファイルを編集してコピー
COPY ./docker/apache/www.conf /etc/php-fpm.d/www.conf

#apacheとphpーfpmの自動起動を有効化
RUN exec systemctl enable httpd
RUN exec systemctl enable php-fpm

#作業ディレクトリを作成
WORKDIR /work


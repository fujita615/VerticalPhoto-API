#apache
FROM httpd:2.4.54

#dockerでパッケージ管理ツールaptが使えるようupdate
RUN apt update -y \
#続けてaptを使って諸々モジュールをインストール
  && apt install -y  tar zip unzip vim wget cron rsyslog curl
RUN apt update \
  && apt install -y locales 
RUN localedef -f UTF-8 -i ja_JP ja_JP.UTF-8

#timezoneを日本に設定
ENV TZ=Asia/Tokyo \
# localeを日本用に設定
LANG=ja_JP.UTF-8 \
LANGUAGE=ja_JP:ja \
LC_ALL=ja_JP.UTF-8

COPY ./docker/web/httpd.conf /usr/local/apache2/conf/httpd.conf

WORKDIR /usr/local/apache2/htdocs
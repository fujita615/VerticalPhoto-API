FROM mysql:8.3.0
#microdnfコマンドを使ってyumを、続けて日本対応localeをインストール
RUN microdnf install yum \
  && dnf install -y glibc-langpack-ja glibc-locale-source git  \
  tar \
  zip \
  unzip \
  vim
#timezoneと言語を日本に設定
ENV TZ=Asia/Tokyo \
  LANG=ja\_JP.UTF-8


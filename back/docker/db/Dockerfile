FROM mysql:8.3.0
#yumを、続けて日本対応localeをインストール
RUN microdnf install yum \
  && dnf install -y glibc-langpack-ja glibc-locale-source git  \
  tar \
  zip \
  unzip \
  vim
COPY ./docker/db/my.conf /etc/my.conf
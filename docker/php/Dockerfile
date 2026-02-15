FROM composer:2.9.5 AS composer
FROM spiralscout/roadrunner:2025.1 AS roadrunner
FROM mlocati/php-extension-installer:2.9 AS php-extension-installer
FROM php:8.3-cli-bookworm

COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY --from=roadrunner /usr/bin/rr /usr/bin/rr
COPY --from=php-extension-installer /usr/bin/install-php-extensions /usr/bin/install-php-extensions

ARG UID=10001
ARG GID=10001

RUN <<EOF
  set -xe
  groupmod --gid=${GID} www-data
  usermod --uid=${UID} --gid=${GID} www-data
EOF

RUN <<EOF
  set -xe
  apt-get update
  apt-get install --no-install-recommends --no-install-suggests -q -y \
          postgresql-client \
          unzip
EOF

RUN <<EOF
  set -xe
  install-php-extensions opcache intl pcntl sockets protobuf bcmath pdo_pgsql zip
  apt-get remove -q -y \
          ${PHPIZE_DEPS} \
          ${BUILD_DEPENDS}
EOF

ENV PATH /var/www/.composer/vendor/bin:$PATH

USER www-data

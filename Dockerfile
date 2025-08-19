FROM php:8.2.4-apache

# Copia o Composer CLI para o container
COPY --from=composer/composer:latest-bin /composer /usr/bin/composer

RUN apt-get update && apt-get install -y \
    wget \
    unzip \
    libaio1 \
    libaio-dev \
    cron \
    git \
    vim \
    && rm -rf /var/lib/apt/lists/*

RUN mkdir -p /opt/oracle
WORKDIR /tmp
ADD oracle/instantclient-basic-linux.x64-21.4.0.0.0dbru.zip .
ADD oracle/instantclient-sdk-linux.x64-21.4.0.0.0dbru.zip .
RUN unzip instantclient-basic-linux.x64-21.4.0.0.0dbru.zip -d /opt/oracle \
    && unzip instantclient-sdk-linux.x64-21.4.0.0.0dbru.zip -d /opt/oracle
RUN echo /opt/oracle/instantclient_21_4 > /etc/ld.so.conf.d/oracle-instantclient.conf \
    && ldconfig

# Instala extensões PHP
RUN docker-php-ext-configure oci8 --with-oci8=instantclient,/opt/oracle/instantclient_21_4 \
    && docker-php-ext-install oci8
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Habilita o módulo rewrite do Apache
RUN a2enmod rewrite

WORKDIR /var/www/html

# Copia composer.json e composer.lock (se existirem) para o diretório de trabalho
COPY composer.json composer.lock ./

# Instala as dependências do Composer
RUN composer install --no-dev --optimize-autoloader

# Copia o restante do seu código da aplicação
ADD src/enviarCenso.php /var/www/html/src/
ADD includes /var/www/html/includes/

RUN chmod -R 777 /var/www/html

ENV TZ="America/Sao_Paulo"

ADD crontab /etc/cron.d/php-cronjob
RUN chmod 0644 /etc/cron.d/php-cronjob
RUN touch /var/log/cron.log

ADD entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Configuração do Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Comando para iniciar o Apache e o cron (do seu entrypoint.sh)
CMD ["/entrypoint.sh"]
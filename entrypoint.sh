#!/bin/bash
set -e

echo "===> Starting cron..."
# O 'printenv > /etc/environment' é opcional aqui, mas pode ajudar a garantir
# que as variáveis injetadas pelo K8s sejam visíveis para os cron jobs.
printenv > /etc/environment
cron -f &
tail -F /var/log/cron.log & # Use -F para seguir o arquivo se ele for recriado

echo "===> Calling official PHP Apache entrypoint..."
# Execute o entrypoint padrão da imagem PHP Apache.
# Ele será responsável por configurar o Apache corretamente e garantir que
# as variáveis de ambiente do contêiner sejam passadas para o Apache e,
# por sua vez, para o mod_php, e então iniciar 'apache2-foreground'.
exec /usr/local/bin/docker-php-entrypoint apache2-foreground

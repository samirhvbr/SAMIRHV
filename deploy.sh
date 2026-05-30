#!/bin/bash
# versão 1.0 - 2024-06-01

set -e  # para imediatamente se qualquer comando falhar

DIR="/srv/www/samirhv"
APP="$DIR/samirhv"

echo "==> Iniciando deploy..."

cd "$DIR"
git pull origin master

cd "$APP"

echo "==> Ativando modo de manutenção..."
php artisan down

echo "==> Instalando dependências PHP..."
composer install --no-dev --optimize-autoloader

echo "==> Compilando assets..."
npm install
npm run build

echo "==> Rodando migrations..."
php artisan migrate --force

echo "==> Reconstruindo cache de produção..."
php artisan optimize:clear
php artisan optimize

echo "==> Desativando modo de manutenção..."
php artisan up

echo "==> Deploy concluído."

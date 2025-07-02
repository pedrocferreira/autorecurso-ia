#!/bin/bash

# Script para corrigir permissões do Laravel
# AutoRecurso IA - Sistema de Recursos de Multas

echo "🔧 Corrigindo permissões do Laravel..."

# Diretório da aplicação
APP_DIR="/var/www/autorecurso-ia"

# Corrigir proprietário
echo "📝 Definindo proprietário correto (www-data)..."
chown -R www-data:www-data "$APP_DIR/storage"
chown -R www-data:www-data "$APP_DIR/bootstrap/cache"

# Corrigir permissões dos diretórios
echo "📁 Definindo permissões dos diretórios (775)..."
find "$APP_DIR/storage" -type d -exec chmod 775 {} \;
find "$APP_DIR/bootstrap/cache" -type d -exec chmod 775 {} \;

# Corrigir permissões dos arquivos
echo "📄 Definindo permissões dos arquivos (664)..."
find "$APP_DIR/storage" -type f -exec chmod 664 {} \;
find "$APP_DIR/bootstrap/cache" -type f -exec chmod 664 {} \;

# Limpar caches
echo "🧹 Limpando caches..."
cd "$APP_DIR"
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Recarregar serviços
echo "🔄 Recarregando serviços..."
systemctl reload nginx
systemctl reload php8.2-fpm

echo "✅ Permissões corrigidas com sucesso!"
echo "🌐 Sua aplicação Laravel deve estar funcionando normalmente agora." 
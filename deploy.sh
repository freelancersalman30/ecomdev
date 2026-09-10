#!/bin/bash
# 🚀 1-Click DREAMERS PCB Live Server Deployer (dreamerspcb.com)
set -e

echo "=================================================="
echo "🚀 Connecting & Syncing with GitHub (main branch)..."
echo "=================================================="

git fetch origin main
git reset --hard origin/main

echo "📁 Setting file and folder permissions..."
chmod -R 755 .
chmod -R 775 storage bootstrap/cache public/uploads 2>/dev/null || true

echo "🔗 Checking storage symlink..."
php artisan storage:link 2>/dev/null || true

echo "⚡ Optimizing application for dreamerspcb.com..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ""
echo "=================================================="
echo "✅ SUCCESS! Your live main domain is connected & live at: https://dreamerspcb.com"
echo "=================================================="

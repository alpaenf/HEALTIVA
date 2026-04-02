#!/bin/bash

# Script Deploy MEDIX
# Cara penggunaan: ./deploy.sh
# Pastikan script ini dijalankan dari dalam root folder project (medix)

# Konfigurasi Path (Sesuaikan jika perlu)
# Asumsi: Folder project 'healtiva' dan 'public_html' berada di level yang sama di hosting
PROJECT_FOLDER="healtiva"
PUBLIC_HTML_PATH="../public_html"

echo "========================================"
echo "🚀 Memulai Deployment MEDIX"
echo "========================================"

# 1. Git Pull
echo "📥 1. Mengambil update terbaru dari Git..."
git pull origin main

if [ $? -ne 0 ]; then
    echo "❌ Gagal melakukan git pull. Cek koneksi atau konflik. (Lanjut proses...)"
fi

# 1.2 Build frontend assets (public/build tidak disimpan di git)
echo "🛠️  1.2. Build aset frontend (Vite)..."
if command -v npm >/dev/null 2>&1; then
    if [ -f package-lock.json ]; then
        npm ci --no-audit --no-fund || npm install --no-audit --no-fund
    else
        npm install --no-audit --no-fund
    fi
    npm run build
    if [ $? -ne 0 ]; then
        echo "❌ Build frontend gagal. Proses deploy dihentikan."
        exit 1
    fi
else
    if [ -f "public/build/manifest.json" ]; then
        echo "⚠️  npm tidak ditemukan. Lewati build server dan gunakan aset public/build yang sudah ada dari git."
    else
        echo "❌ npm tidak ditemukan dan public/build/manifest.json tidak ada."
        echo "   Solusi: build di lokal/CI lalu commit folder public/build."
        exit 1
    fi
fi

# 1.5 Check .env configuration
echo "🔧 1.5. Memeriksa konfigurasi .env..."
if ! grep -q "SESSION_SECURE_COOKIE=true" .env 2>/dev/null; then
    echo "⚠️  Warning: SESSION_SECURE_COOKIE belum di-set ke true di .env"
    echo "   Pastikan update .env dengan konfigurasi yang tepat untuk production."
fi

# 2. Pindahkan isi folder public ke public_html
echo "📂 2. Menyalin aset dari folder public ke $PUBLIC_HTML_PATH..."
# Buat folder jika belum ada (opsional, jaga-jaga)
mkdir -p "$PUBLIC_HTML_PATH"
# Menyalin semua isi folder public ke public_html
cp -r public/* "$PUBLIC_HTML_PATH/"

# Salin atau Buat .htaccess standard Laravel
echo "📝 Menyalin/Membuat .htaccess di public_html pengarah route..."
cat > "$PUBLIC_HTML_PATH/.htaccess" << 'EOF'
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
EOF

# 3. Update index.php
echo "📝 3. Memperbarui index.php di public_html..."
cat > "$PUBLIC_HTML_PATH/index.php" << EOL
<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists(\$maintenance = __DIR__.'/../$PROJECT_FOLDER/storage/framework/maintenance.php')) {
    require \$maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../$PROJECT_FOLDER/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application \$app */
\$app = require_once __DIR__.'/../$PROJECT_FOLDER/bootstrap/app.php';

\$app->handleRequest(Request::capture());
EOL

# 4. Fix Storage Symlink
echo "🔗 4. Memperbaiki Symbolic Link Storage..."
# Hapus link/folder storage lama di public_html untuk memastikan bersih
rm -rf "$PUBLIC_HTML_PATH/storage"

# Buat symlink baru yang benar
# Dari: public_html/storage
# Ke:   ../MEDIX/storage/app/public
ln -s "../$PROJECT_FOLDER/storage/app/public" "$PUBLIC_HTML_PATH/storage"

# 5. Clear Laravel caches
echo "🧹 5. Membersihkan cache Laravel..."
php artisan optimize:clear

echo "========================================"
echo "✅ Deployment MEDIX Selesai!"
echo "========================================"

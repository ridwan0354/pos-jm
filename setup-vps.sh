#!/bin/bash
set -e

echo "================================================="
echo "  Memulai Instalasi Server untuk LinenFlow POS..."
echo "================================================="

echo ""
echo "=> 1. Konfigurasi Database MySQL..."
# Pastikan MySQL berjalan
systemctl start mysql

# Membuat database dan user untuk proyek kedua
mysql -u root -e "CREATE DATABASE IF NOT EXISTS pos_jm;"
mysql -u root -e "CREATE USER IF NOT EXISTS 'pos_jm_user'@'localhost' IDENTIFIED BY 'PasswordLinen123!';"
mysql -u root -e "GRANT ALL PRIVILEGES ON pos_jm.* TO 'pos_jm_user'@'localhost';"
mysql -u root -e "FLUSH PRIVILEGES;"

echo ""
echo "=> 2. Menarik kode dari GitHub..."
cd /var/www
git config --global --add safe.directory /var/www/pos-jm || true

if [ -d "pos-jm" ]; then
    echo "Folder /var/www/pos-jm sudah ada, melakukan pembaruan..."
    cd pos-jm
    git reset --hard
    git pull origin main
else
    git clone https://github.com/ridwan0354/pos-jm.git pos-jm
    cd pos-jm
fi

echo ""
echo "=> 3. Mengatur berkas konfigurasi .env..."
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Hapus pengaturan DB lama di .env jika ada
sed -i '/^DB_/d' .env
sed -i '/^# DB_/d' .env

# Tambahkan pengaturan DB baru
echo "" >> .env
echo "DB_CONNECTION=mysql" >> .env
echo "DB_HOST=127.0.0.1" >> .env
echo "DB_PORT=3306" >> .env
echo "DB_DATABASE=pos_jm" >> .env
echo "DB_USERNAME=pos_jm_user" >> .env
echo "DB_PASSWORD=PasswordLinen123!" >> .env

# Atur APP_URL ke subdomain laundry
sed -i 's|APP_URL=.*|APP_URL=https://laundry.galipatsistem.com|' .env
sed -i 's|APP_ENV=.*|APP_ENV=production|' .env
sed -i 's|APP_DEBUG=.*|APP_DEBUG=false|' .env

echo ""
echo "=> 4. Menginstal Composer dan NPM..."
export COMPOSER_ALLOW_SUPERUSER=1

# Jalankan composer install
composer install --no-dev --optimize-autoloader

# Jalankan migrasi database dan seeders bawaan
php artisan key:generate --force
php artisan migrate --force
php artisan db:seed --force
php artisan optimize

# Jalankan npm install dan build aset frontend
if ! command -v node &> /dev/null; then
    curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
    apt-get install -y nodejs
fi
npm install
npm run build

echo ""
echo "=> 5. Mengatur hak akses berkas (permissions)..."
chown -R www-data:www-data /var/www/pos-jm
chmod -R 775 /var/www/pos-jm/storage /var/www/pos-jm/bootstrap/cache

echo ""
echo "=> 6. Konfigurasi Nginx Web Server..."
cat <<EOF > /etc/nginx/sites-available/pos-jm
server {
    listen 80;
    server_name laundry.galipatsistem.com;
    root /var/www/pos-jm/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php\$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF

# Aktifkan konfigurasi di nginx
ln -sf /etc/nginx/sites-available/pos-jm /etc/nginx/sites-enabled/
nginx -t
systemctl restart nginx

echo "================================================="
echo " Instalasi LinenFlow POS Selesai!"
echo " Silakan buka browser Anda di alamat:"
echo " https://laundry.galipatsistem.com"
echo "================================================="

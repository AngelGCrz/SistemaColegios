# Guía de Despliegue — SistemaColegios

## Opción A: VPS con Ubuntu + Nginx (Recomendado)

### Requisitos del servidor
- Ubuntu 22.04 LTS
- 2GB RAM mínimo (4GB recomendado)
- 20GB SSD
- PHP 8.2+, MySQL 8.0+, Nginx, Composer, Git

### Paso 1: Preparar el servidor

```bash
# Actualizar sistema
sudo apt update && sudo apt upgrade -y

# Instalar PHP 8.2 y extensiones
sudo add-apt-repository ppa:ondrej/php -y
sudo apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml \
    php8.2-bcmath php8.2-curl php8.2-zip php8.2-gd php8.2-intl

# Instalar MySQL
sudo apt install -y mysql-server
sudo mysql_secure_installation

# Instalar Nginx
sudo apt install -y nginx

# Instalar Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Instalar Node.js (para compilar assets en producción)
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
```

### Paso 2: Crear base de datos

```bash
sudo mysql -u root -p
```
```sql
CREATE DATABASE sistema_colegios CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'colegios_user'@'localhost' IDENTIFIED BY 'contraseña_segura_aqui';
GRANT ALL PRIVILEGES ON sistema_colegios.* TO 'colegios_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Paso 3: Desplegar la aplicación

```bash
# Clonar repositorio
cd /var/www
sudo git clone <tu-repositorio> sistema-colegios
cd sistema-colegios

# Permisos
sudo chown -R www-data:www-data /var/www/sistema-colegios
sudo chmod -R 775 storage bootstrap/cache

# Instalar dependencias
composer install --optimize-autoloader --no-dev

# Configurar entorno
cp .env.example .env
nano .env
# Editar: DB_DATABASE, DB_USERNAME, DB_PASSWORD, APP_URL, APP_ENV=production, APP_DEBUG=false

# Generar key y migrar
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force  # Solo si hay seeder
php artisan storage:link

# Optimizar
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### Paso 4: Configurar Nginx

```bash
sudo nano /etc/nginx/sites-available/sistema-colegios
```

```nginx
server {
    listen 80;
    server_name tu-dominio.com www.tu-dominio.com;
    root /var/www/sistema-colegios/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    client_max_body_size 20M;
}
```

```bash
sudo ln -s /etc/nginx/sites-available/sistema-colegios /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### Paso 5: SSL con Let's Encrypt

```bash
sudo apt install certbot python3-certbot-nginx -y
sudo certbot --nginx -d tu-dominio.com -d www.tu-dominio.com
```

---

## Opción B: VPS con CyberPanel

CyberPanel simplifica la administración para quienes prefieren un panel web.

1. **Instalar CyberPanel**: `sh <(curl https://cyberpanel.net/install.sh || wget -O - https://cyberpanel.net/install.sh)`
2. **Crear sitio web** desde el panel → tu-dominio.com
3. **Apuntar Document Root** a `public/`
4. **Subir código** vía File Manager o SSH
5. **Crear BD** desde CyberPanel → Database
6. **Ejecutar** los mismos pasos de artisan (migrate, seed, cache, etc.)
7. **SSL**: CyberPanel tiene integración automática con Let's Encrypt

---

## Configuración de Producción (.env)

```env
APP_NAME="SistemaColegios"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sistema_colegios
DB_USERNAME=colegios_user
DB_PASSWORD=contraseña_segura_aqui

SESSION_DRIVER=database
CACHE_STORE=file

MAIL_MAILER=smtp
MAIL_HOST=smtp.tu-proveedor.com
MAIL_PORT=587
MAIL_USERNAME=notificaciones@tu-dominio.com
MAIL_PASSWORD=mail_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=notificaciones@tu-dominio.com
MAIL_FROM_NAME="SistemaColegios"
```

---

## Mantenimiento

### Actualizar aplicación
```bash
cd /var/www/sistema-colegios
git pull origin main
composer install --optimize-autoloader --no-dev
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Backups (programar con cron)
```bash
# Backup de BD (diario a las 2am)
0 2 * * * mysqldump -u colegios_user -p'password' sistema_colegios | gzip > /backups/db-$(date +\%Y\%m\%d).sql.gz

# Backup de storage
0 3 * * * tar -czf /backups/storage-$(date +\%Y\%m\%d).tar.gz /var/www/sistema-colegios/storage/app
```

### Monitoreo
- Logs: `tail -f storage/logs/laravel.log`
- Nginx: `tail -f /var/log/nginx/error.log`
- PHP-FPM: `tail -f /var/log/php8.2-fpm.log`

---

## Escalamiento futuro

| Fase | Acción |
|---|---|
| 1-50 colegios | Un solo VPS (4GB RAM) |
| 50-200 colegios | Separar BD a servidor dedicado, agregar Redis |
| 200+ colegios | Load balancer, réplicas de lectura, CDN |
| Personalización | Subdominio por colegio (colegio.sistemacolegios.com) |

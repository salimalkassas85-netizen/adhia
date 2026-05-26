# Eid Al-Adha Gift Initiative

Laravel Blade application for a local Eid Al-Adha meat gift initiative in one markaz/district.

## Production Stack

- PHP 8.2+
- MySQL 8+ or MariaDB 10.6+
- Laravel 12
- HTTPS-enabled web server
- OpenStreetMap/Leaflet for maps, with no paid Google Maps API key

## Environment

Copy `.env.example` to `.env` and set production values:

```env
APP_NAME="Eid Gift Initiative"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.example

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=adhia
DB_USERNAME=adhia_user
DB_PASSWORD=strong-password

SESSION_DRIVER=database
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax

QUEUE_CONNECTION=database
CACHE_STORE=database
```

## Install

Create the MySQL database and user first:

```sql
CREATE DATABASE adhia CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'adhia_user'@'localhost' IDENTIFIED BY 'strong-password';
GRANT ALL PRIVILEGES ON adhia.* TO 'adhia_user'@'localhost';
FLUSH PRIVILEGES;
```

```bash
composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Create First Admin

```bash
php artisan initiative:create-admin admin@example.com --name="Admin"
```

The admin must accept the pledge at `/pledge` before opening the dashboard.

## Operations

- Create areas from `/admin/areas`.
- Create delivery agents from `/admin/agents`.
- Public gift requests are submitted at `/request-gift`.
- Public donations are submitted at `/donate`.
- Agents only see assigned requests at `/agent/requests`.

## Maps

The app uses browser Geolocation plus Leaflet/OpenStreetMap. No Google Maps billing or API key is required.

For location buttons to work in production, serve the site over HTTPS. Browsers block precise geolocation on insecure origins except localhost.

## Production Checks

```bash
php artisan test
vendor/bin/pint --test
```

Recommended server tasks:

- Run the queue worker with Supervisor/systemd if queued work is added later.
- Run `php artisan schedule:run` every minute if scheduled jobs are added later.
- Keep `APP_DEBUG=false`.
- Restrict database credentials to this application database only.
- Take regular encrypted MySQL backups because beneficiary and donor location data is sensitive.

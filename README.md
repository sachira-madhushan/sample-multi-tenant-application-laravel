
# Sample Laravel Multitenant Application

This is a **sample multitenant Laravel application** designed to demonstrate how to manage multiple tenant databases, authentication, and isolated routes.It includes guides for **local testing with Herd**, **deployment to production with Nginx**, and general project setup.

---




## Features

- 🏢 Multi-Tenant Architecture (Database-per-tenant approach)
- 🔐 Tenant-specific Authentication (Login/Register per tenant)
- 🌐 Tenant-based Routing (Dynamic DB connection per request)
- 🛠️ RESTful API for tenants
- 🐘 MySQL supported
- 🖥️ Local development supported with **Herd** (Mac/Windows)


## Installation

### 1️⃣ Clone the Repository
```bash
git clone https://github.com/DI11SOFT/sample-multi-tenant-laravel-application.git
cd sample-multi-tenant-laravel-application
```

### 2️⃣ Install Dependencies
```
composer install
```

### 3️⃣ Configure Environment

Copy the example .env file and update your credentials:
```
cp .env.example .env
```

Edit .env and set:

```
APP_NAME="Laravel Multitenant"
APP_ENV=local
APP_KEY=base64:GENERATE_KEY
APP_DEBUG=true
APP_URL=http://multitenant.local

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=central_db
DB_USERNAME=root
DB_PASSWORD=

TENANCY_DOMAIN_IDENTIFICATION=true
TENANCY_DB_CONNECTION=mysql

SESSION_DOMAIN=.multitenant.local

```

### 4️⃣ Generate App Key
```
php artisan key:generate

```

### 5️⃣ Run Migrations

```
php artisan migrate
```


## Local Testing with Herd

Herd is the official Laravel local development tool.
Steps:

Install Herd on your system.

Create a local domain mapping:
```

herd link
```

Example:
```
http://multitenant.local
```

Update .env:
```
APP_URL=http://multitenant.local
```

Start Herd:
```
herd start
```

Open browser at:
```
http://multitenant.local
```



## 🌍 Hosting Guide (Production)

Server Requirements
```
PHP 8.2+

MySQL 8 / MariaDB

Composer

Nginx / Apache

```
Deployment Steps

```
Upload project files to your server (/var/www/multitenant).
```

Install dependencies:
```
composer install --optimize-autoloader --no-dev
```

Set correct permissions:
```
sudo chown -R www-data:www-data /var/www/multitenant
sudo chmod -R 775 /var/www/multitenant/storage /var/www/multitenant/bootstrap/cache
```

Configure .env with production database credentials.

Run migrations:
```
php artisan migrate --force
```

Cache configs for performance:
```
php artisan config:cache
php artisan route:cache
php artisan view:cache
```
## ⚙️ Nginx Configuration

Add a new server block in /etc/nginx/sites-available/multitenant:
```
server {
    listen 80;
    server_name multitenant.com *.multitenant.com;

    root /var/www/multitenant/public;

    index index.php index.html;

    access_log /var/log/nginx/multitenant_access.log;
    error_log /var/log/nginx/multitenant_error.log;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}

```

## ⚙️ DNS Configuration

Forward all main domain and all subdomain to the VPS from the DNS configurations of the domain and add A records to the DNS configurations records to point main domain and all subdomain to point to the VPS.

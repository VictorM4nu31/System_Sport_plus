# Deployment Guide

This guide covers deploying the e-commerce system to production environments.

## Prerequisites

### Server Requirements

- **PHP**: 8.1 or higher with required extensions
- **Web Server**: Apache 2.4+ or Nginx 1.18+
- **Database**: MySQL 5.7+ or MariaDB 10.3+
- **SSL Certificate**: Required for payment processing
- **Memory**: Minimum 512MB RAM (2GB+ recommended)
- **Storage**: Minimum 1GB free space

### Required PHP Extensions

```bash
# Check required extensions
php -m | grep -E "(openssl|pdo|mbstring|tokenizer|xml|ctype|json|bcmath|curl|fileinfo|gd)"
```

Required extensions:
- openssl
- pdo_mysql
- mbstring
- tokenizer
- xml
- ctype
- json
- bcmath
- curl
- fileinfo
- gd (for image processing)

## Pre-Deployment Checklist

### 1. Environment Configuration

Create production `.env` file:

```env
# Application
APP_NAME="Your Store Name"
APP_ENV=production
APP_KEY=base64:your_generated_key_here
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=mysql
DB_HOST=your_db_host
DB_PORT=3306
DB_DATABASE=your_production_db
DB_USERNAME=your_db_user
DB_PASSWORD=your_secure_password

# Stripe (Production Keys)
STRIPE_KEY=pk_live_your_live_publishable_key
STRIPE_SECRET=sk_live_your_live_secret_key
STRIPE_WEBHOOK_SECRET=whsec_your_live_webhook_secret

# Security
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true

# Caching
CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=your_redis_password
REDIS_PORT=6379

# Queue
QUEUE_CONNECTION=redis

# Mail
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"

# Logging
LOG_CHANNEL=daily
LOG_LEVEL=error
```

### 2. Security Configuration

#### File Permissions

```bash
# Set proper ownership
sudo chown -R www-data:www-data /path/to/your/app

# Set directory permissions
find /path/to/your/app -type d -exec chmod 755 {} \;

# Set file permissions
find /path/to/your/app -type f -exec chmod 644 {} \;

# Set storage and cache permissions
chmod -R 775 storage bootstrap/cache
```

#### Environment File Security

```bash
# Secure .env file
chmod 600 .env
chown www-data:www-data .env
```

### 3. Database Setup

```bash
# Create production database
mysql -u root -p
CREATE DATABASE your_production_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'your_db_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON your_production_db.* TO 'your_db_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

## Deployment Methods

### Method 1: Manual Deployment

#### Step 1: Upload Files

```bash
# Upload application files (excluding sensitive files)
rsync -avz --exclude='.env' --exclude='node_modules' --exclude='.git' \
  /local/path/to/app/ user@server:/path/to/production/app/
```

#### Step 2: Install Dependencies

```bash
# On production server
cd /path/to/production/app

# Install PHP dependencies (production only)
composer install --no-dev --optimize-autoloader

# Install and build frontend assets
npm ci --production
npm run build
```

#### Step 3: Configure Application

```bash
# Generate application key
php artisan key:generate

# Run database migrations
php artisan migrate --force

# Seed initial data (if needed)
php artisan db:seed --class=ProductionSeeder

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create storage symlink
php artisan storage:link
```

### Method 2: Automated Deployment with CI/CD

#### GitHub Actions Example

Create `.github/workflows/deploy.yml`:

```yaml
name: Deploy to Production

on:
  push:
    branches: [main]

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.1'
        extensions: mbstring, xml, ctype, iconv, intl, pdo_mysql
    
    - name: Install dependencies
      run: composer install --no-dev --optimize-autoloader
    
    - name: Build assets
      run: |
        npm ci
        npm run build
    
    - name: Deploy to server
      uses: appleboy/ssh-action@v0.1.5
      with:
        host: ${{ secrets.HOST }}
        username: ${{ secrets.USERNAME }}
        key: ${{ secrets.SSH_KEY }}
        script: |
          cd /path/to/production/app
          git pull origin main
          composer install --no-dev --optimize-autoloader
          npm ci --production
          npm run build
          php artisan migrate --force
          php artisan config:cache
          php artisan route:cache
          php artisan view:cache
          sudo systemctl reload php8.1-fpm
          sudo systemctl reload nginx
```

## Web Server Configuration

### Nginx Configuration

Create `/etc/nginx/sites-available/your-domain`:

```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;
    
    root /path/to/production/app/public;
    index index.php index.html;
    
    # SSL Configuration
    ssl_certificate /path/to/ssl/certificate.crt;
    ssl_certificate_key /path/to/ssl/private.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512:ECDHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;
    
    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;
    
    # Rate Limiting
    limit_req_zone $binary_remote_addr zone=api:10m rate=10r/m;
    limit_req_zone $binary_remote_addr zone=login:10m rate=5r/m;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }
    
    # API Rate Limiting
    location /api/ {
        limit_req zone=api burst=20 nodelay;
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    # Login Rate Limiting
    location /login {
        limit_req zone=login burst=5 nodelay;
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    # Stripe Webhook (no rate limiting)
    location /stripe/webhook {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    # Static Assets
    location ~* \.(css|js|jpg|jpeg|png|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
    
    # Security
    location ~ /\.(?!well-known).* {
        deny all;
    }
    
    location ~ ^/(\.env|composer\.(json|lock)|package\.json|artisan) {
        deny all;
    }
}
```

Enable the site:

```bash
sudo ln -s /etc/nginx/sites-available/your-domain /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### Apache Configuration

Create virtual host configuration:

```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    ServerAlias www.yourdomain.com
    Redirect permanent / https://yourdomain.com/
</VirtualHost>

<VirtualHost *:443>
    ServerName yourdomain.com
    ServerAlias www.yourdomain.com
    DocumentRoot /path/to/production/app/public
    
    # SSL Configuration
    SSLEngine on
    SSLCertificateFile /path/to/ssl/certificate.crt
    SSLCertificateKeyFile /path/to/ssl/private.key
    SSLProtocol all -SSLv3 -TLSv1 -TLSv1.1
    SSLCipherSuite ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384
    
    # Security Headers
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-XSS-Protection "1; mode=block"
    Header always set X-Content-Type-Options "nosniff"
    Header always set Referrer-Policy "no-referrer-when-downgrade"
    
    # PHP Configuration
    <FilesMatch \.php$>
        SetHandler "proxy:unix:/var/run/php/php8.1-fpm.sock|fcgi://localhost"
    </FilesMatch>
    
    # Laravel Configuration
    <Directory /path/to/production/app/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    # Security
    <Files ".env">
        Require all denied
    </Files>
    
    <Files "composer.json">
        Require all denied
    </Files>
    
    <Files "composer.lock">
        Require all denied
    </Files>
</VirtualHost>
```

## SSL Certificate Setup

### Using Let's Encrypt (Certbot)

```bash
# Install Certbot
sudo apt update
sudo apt install certbot python3-certbot-nginx

# Obtain certificate
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com

# Test automatic renewal
sudo certbot renew --dry-run
```

### Using Custom SSL Certificate

```bash
# Copy certificate files
sudo cp your-certificate.crt /etc/ssl/certs/
sudo cp your-private-key.key /etc/ssl/private/
sudo chmod 644 /etc/ssl/certs/your-certificate.crt
sudo chmod 600 /etc/ssl/private/your-private-key.key
```

## Database Optimization

### MySQL Configuration

Add to `/etc/mysql/mysql.conf.d/mysqld.cnf`:

```ini
[mysqld]
# Performance
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
innodb_flush_log_at_trx_commit = 2
innodb_flush_method = O_DIRECT

# Connection limits
max_connections = 200
max_user_connections = 180

# Query cache (if using MySQL 5.7)
query_cache_type = 1
query_cache_size = 128M

# Logging
slow_query_log = 1
slow_query_log_file = /var/log/mysql/slow.log
long_query_time = 2
```

### Database Indexing

Ensure proper indexes are in place:

```sql
-- Product search optimization
CREATE INDEX idx_products_name ON products(name);
CREATE INDEX idx_products_category ON products(category_id);
CREATE INDEX idx_products_stock ON products(stock);

-- Order optimization
CREATE INDEX idx_orders_user_status ON orders(user_id, status);
CREATE INDEX idx_orders_created_at ON orders(created_at);

-- Stock reservations cleanup
CREATE INDEX idx_stock_reservations_expires ON stock_reservations(expires_at);

-- Security logs
CREATE INDEX idx_security_logs_created_at ON security_logs(created_at);
CREATE INDEX idx_security_logs_event_type ON security_logs(event_type);
```

## Caching Setup

### Redis Configuration

Install and configure Redis:

```bash
# Install Redis
sudo apt update
sudo apt install redis-server

# Configure Redis
sudo nano /etc/redis/redis.conf
```

Redis configuration:

```ini
# Security
requirepass your_redis_password
bind 127.0.0.1

# Memory management
maxmemory 512mb
maxmemory-policy allkeys-lru

# Persistence
save 900 1
save 300 10
save 60 10000
```

### Laravel Cache Configuration

```bash
# Clear existing cache
php artisan cache:clear

# Set cache driver to Redis
php artisan config:cache
```

## Queue Setup

### Supervisor Configuration

Create `/etc/supervisor/conf.d/laravel-worker.conf`:

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/production/app/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/production/app/storage/logs/worker.log
stopwaitsecs=3600
```

Start the worker:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

## Monitoring Setup

### Log Rotation

Create `/etc/logrotate.d/laravel`:

```
/path/to/production/app/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    notifempty
    create 644 www-data www-data
    postrotate
        /usr/bin/supervisorctl restart laravel-worker:*
    endscript
}
```

### Health Check Endpoint

Create a health check route in `routes/web.php`:

```php
Route::get('/health', function () {
    $checks = [
        'database' => false,
        'redis' => false,
        'storage' => false,
        'stripe' => false
    ];
    
    try {
        DB::connection()->getPdo();
        $checks['database'] = true;
    } catch (Exception $e) {}
    
    try {
        Redis::ping();
        $checks['redis'] = true;
    } catch (Exception $e) {}
    
    try {
        Storage::disk('local')->put('health-check', 'ok');
        Storage::disk('local')->delete('health-check');
        $checks['storage'] = true;
    } catch (Exception $e) {}
    
    try {
        $stripe = new \Stripe\StripeClient(config('stripe.secret'));
        $stripe->accounts->retrieve();
        $checks['stripe'] = true;
    } catch (Exception $e) {}
    
    $allHealthy = !in_array(false, $checks);
    
    return response()->json([
        'status' => $allHealthy ? 'healthy' : 'unhealthy',
        'checks' => $checks,
        'timestamp' => now()->toISOString()
    ], $allHealthy ? 200 : 503);
});
```

### Monitoring Script

Create monitoring script `/usr/local/bin/monitor-app.sh`:

```bash
#!/bin/bash

APP_URL="https://yourdomain.com"
HEALTH_ENDPOINT="$APP_URL/health"
LOG_FILE="/var/log/app-monitor.log"
ALERT_EMAIL="admin@yourdomain.com"

# Check application health
response=$(curl -s -o /dev/null -w "%{http_code}" "$HEALTH_ENDPOINT")

if [ "$response" != "200" ]; then
    echo "$(date): Application health check failed (HTTP $response)" >> "$LOG_FILE"
    echo "Application health check failed" | mail -s "App Alert: Health Check Failed" "$ALERT_EMAIL"
fi

# Check disk space
disk_usage=$(df /path/to/production/app | awk 'NR==2 {print $5}' | sed 's/%//')
if [ "$disk_usage" -gt 80 ]; then
    echo "$(date): Disk usage is at $disk_usage%" >> "$LOG_FILE"
    echo "Disk usage is at $disk_usage%" | mail -s "App Alert: High Disk Usage" "$ALERT_EMAIL"
fi

# Check queue workers
worker_count=$(supervisorctl status laravel-worker:* | grep RUNNING | wc -l)
if [ "$worker_count" -lt 2 ]; then
    echo "$(date): Only $worker_count queue workers running" >> "$LOG_FILE"
    echo "Only $worker_count queue workers running" | mail -s "App Alert: Queue Workers Down" "$ALERT_EMAIL"
fi
```

Add to crontab:

```bash
# Monitor every 5 minutes
*/5 * * * * /usr/local/bin/monitor-app.sh
```

## Backup Strategy

### Database Backup

Create backup script `/usr/local/bin/backup-db.sh`:

```bash
#!/bin/bash

DB_NAME="your_production_db"
DB_USER="your_db_user"
DB_PASS="your_db_password"
BACKUP_DIR="/backups/database"
DATE=$(date +%Y%m%d_%H%M%S)

# Create backup directory
mkdir -p "$BACKUP_DIR"

# Create database backup
mysqldump -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" | gzip > "$BACKUP_DIR/db_backup_$DATE.sql.gz"

# Keep only last 7 days of backups
find "$BACKUP_DIR" -name "db_backup_*.sql.gz" -mtime +7 -delete

echo "Database backup completed: db_backup_$DATE.sql.gz"
```

### File Backup

Create file backup script `/usr/local/bin/backup-files.sh`:

```bash
#!/bin/bash

APP_DIR="/path/to/production/app"
BACKUP_DIR="/backups/files"
DATE=$(date +%Y%m%d_%H%M%S)

# Create backup directory
mkdir -p "$BACKUP_DIR"

# Backup important files
tar -czf "$BACKUP_DIR/files_backup_$DATE.tar.gz" \
    --exclude="$APP_DIR/storage/logs/*" \
    --exclude="$APP_DIR/storage/framework/cache/*" \
    --exclude="$APP_DIR/storage/framework/sessions/*" \
    --exclude="$APP_DIR/storage/framework/views/*" \
    --exclude="$APP_DIR/node_modules" \
    --exclude="$APP_DIR/.git" \
    "$APP_DIR"

# Keep only last 3 days of file backups
find "$BACKUP_DIR" -name "files_backup_*.tar.gz" -mtime +3 -delete

echo "File backup completed: files_backup_$DATE.tar.gz"
```

Schedule backups:

```bash
# Daily database backup at 2 AM
0 2 * * * /usr/local/bin/backup-db.sh

# Weekly file backup on Sundays at 3 AM
0 3 * * 0 /usr/local/bin/backup-files.sh
```

## Security Hardening

### Firewall Configuration

```bash
# Install UFW
sudo apt install ufw

# Default policies
sudo ufw default deny incoming
sudo ufw default allow outgoing

# Allow SSH (change port if needed)
sudo ufw allow 22/tcp

# Allow HTTP and HTTPS
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# Allow MySQL (only from localhost)
sudo ufw allow from 127.0.0.1 to any port 3306

# Enable firewall
sudo ufw enable
```

### Fail2Ban Configuration

Install and configure Fail2Ban:

```bash
sudo apt install fail2ban
```

Create `/etc/fail2ban/jail.local`:

```ini
[DEFAULT]
bantime = 3600
findtime = 600
maxretry = 5

[nginx-http-auth]
enabled = true
port = http,https
logpath = /var/log/nginx/error.log

[nginx-limit-req]
enabled = true
port = http,https
logpath = /var/log/nginx/error.log
maxretry = 10

[sshd]
enabled = true
port = ssh
logpath = /var/log/auth.log
maxretry = 3
```

## Post-Deployment Verification

### 1. Application Health Check

```bash
# Test application response
curl -I https://yourdomain.com

# Test health endpoint
curl https://yourdomain.com/health
```

### 2. Payment System Test

- Test Stripe webhook endpoint
- Verify payment processing with test cards
- Check payment logs for errors

### 3. Security Verification

```bash
# Check SSL certificate
openssl s_client -connect yourdomain.com:443 -servername yourdomain.com

# Test security headers
curl -I https://yourdomain.com
```

### 4. Performance Testing

```bash
# Basic load test with Apache Bench
ab -n 100 -c 10 https://yourdomain.com/

# Test API endpoints
ab -n 50 -c 5 -H "Accept: application/json" https://yourdomain.com/api/products
```

## Troubleshooting

### Common Issues

#### 500 Internal Server Error
- Check Laravel logs: `tail -f storage/logs/laravel.log`
- Verify file permissions
- Check web server error logs

#### Database Connection Issues
- Verify database credentials in `.env`
- Check database server status
- Test connection: `php artisan tinker` then `DB::connection()->getPdo()`

#### Payment Processing Issues
- Verify Stripe keys are correct
- Check webhook endpoint accessibility
- Review payment logs: `tail -f storage/logs/payments.log`

#### Queue Jobs Not Processing
- Check supervisor status: `sudo supervisorctl status`
- Restart workers: `sudo supervisorctl restart laravel-worker:*`
- Check worker logs: `tail -f storage/logs/worker.log`

### Emergency Procedures

#### Application Rollback

```bash
# Stop application
sudo systemctl stop nginx

# Restore from backup
cd /path/to/production
mv app app.broken
tar -xzf /backups/files/files_backup_YYYYMMDD_HHMMSS.tar.gz

# Restore database
mysql -u user -p database_name < /backups/database/db_backup_YYYYMMDD_HHMMSS.sql

# Start application
sudo systemctl start nginx
```

#### Emergency Maintenance Mode

```bash
# Enable maintenance mode
php artisan down --message="Emergency maintenance in progress"

# Disable maintenance mode
php artisan up
```

This deployment guide provides a comprehensive approach to deploying the e-commerce system securely and reliably in production environments.

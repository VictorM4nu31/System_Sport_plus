# Environment Variables Documentation

This document describes all environment variables used in the e-commerce system, their purposes, and configuration requirements.

## Required Variables

These variables are **required** for the system to function properly:

### Application Configuration

```env
# Application name displayed throughout the system
APP_NAME="Your Store Name"

# Environment: local, staging, production
APP_ENV=production

# Application key for encryption (generate with: php artisan key:generate)
APP_KEY=base64:your_generated_key_here

# Debug mode (MUST be false in production)
APP_DEBUG=false

# Application URL (used for links and webhooks)
APP_URL=https://yourdomain.com

# Application locale
APP_LOCALE=es
APP_FALLBACK_LOCALE=es
```

### Database Configuration

```env
# Database connection type
DB_CONNECTION=mysql

# Database server details
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_secure_password
```

### Stripe Payment Configuration

```env
# Stripe publishable key (starts with pk_test_ or pk_live_)
STRIPE_KEY=pk_test_your_publishable_key_here

# Stripe secret key (starts with sk_test_ or sk_live_)
STRIPE_SECRET=sk_test_your_secret_key_here

# Stripe webhook secret (starts with whsec_)
STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret_here
```

## Optional Variables

These variables have default values but can be customized:

### Currency and Localization

```env
# Default currency for payments (ISO 4217 code)
CASHIER_CURRENCY=usd

# Currency locale for formatting
CASHIER_CURRENCY_LOCALE=en

# Stripe webhook tolerance in seconds (default: 300)
STRIPE_WEBHOOK_TOLERANCE=300

# Logging channel for Stripe events (default: payments)
STRIPE_LOGGER=payments
```

### Session Configuration

```env
# Session driver: file, cookie, database, redis
SESSION_DRIVER=database

# Session lifetime in minutes (default: 120)
SESSION_LIFETIME=120

# Enable secure cookies (HTTPS only) - set to true in production
SESSION_SECURE_COOKIE=false

# HTTP only cookies (recommended: true)
SESSION_HTTP_ONLY=true
```

### Caching Configuration

```env
# Cache driver: file, database, redis, memcached
CACHE_DRIVER=database

# Cache prefix to avoid conflicts
CACHE_PREFIX=

# Redis configuration (if using Redis)
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### Queue Configuration

```env
# Queue connection: sync, database, redis, sqs
QUEUE_CONNECTION=database
```

### Mail Configuration

```env
# Mail driver: smtp, sendmail, mailgun, ses, log
MAIL_MAILER=smtp

# SMTP server details
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your_email@example.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls

# From address and name
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Logging Configuration

```env
# Default log channel: single, daily, slack, syslog, errorlog
LOG_CHANNEL=daily

# Log level: emergency, alert, critical, error, warning, notice, info, debug
LOG_LEVEL=error

# Number of days to keep daily logs (default: 14)
LOG_DAILY_DAYS=14

# Log stack channels (comma-separated)
LOG_STACK=single,payments,orders,security
```

## Security Variables

### Authentication and Authorization

```env
# Bcrypt rounds for password hashing (default: 12)
BCRYPT_ROUNDS=12
```

### Security Monitoring

```env
# Enable security event logging (default: true)
SECURITY_LOGGING_ENABLED=true

# Security log channel (default: security)
SECURITY_LOG_CHANNEL=security

# Admin email for critical alerts
ADMIN_EMAIL=admin@yourdomain.com

# Admin phone for SMS alerts (optional)
ADMIN_PHONE=+1234567890
```

## Monitoring Variables

### Health Check Configuration

```env
# Health check interval in minutes (default: 5)
HEALTH_CHECK_INTERVAL=5

# Cache health check results for minutes (default: 2)
HEALTH_CHECK_CACHE=2

# Enable/disable specific health checks
HEALTH_CHECK_DATABASE=true
HEALTH_CHECK_PAYMENTS=true
HEALTH_CHECK_INVENTORY=true
HEALTH_CHECK_QUEUE=true
HEALTH_CHECK_STORAGE=true
HEALTH_CHECK_APPLICATION=true
```

### Alert Thresholds

```env
# Database response time threshold in milliseconds (default: 1000)
DB_RESPONSE_TIME_THRESHOLD=1000

# Long query threshold in seconds (default: 30)
DB_LONG_QUERY_THRESHOLD=30

# Payment failure threshold per hour (default: 5)
PAYMENT_FAILURE_THRESHOLD=5

# Payment processing time threshold in seconds (default: 30)
PAYMENT_PROCESSING_THRESHOLD=30

# Low stock threshold (default: 5)
LOW_STOCK_THRESHOLD=5

# Out of stock threshold (default: 10)
OUT_OF_STOCK_THRESHOLD=10

# Queue failed jobs threshold per hour (default: 10)
QUEUE_FAILED_THRESHOLD=10

# Queue size threshold (default: 1000)
QUEUE_SIZE_THRESHOLD=1000

# Storage usage threshold percentage (default: 85)
STORAGE_USAGE_THRESHOLD=85

# Error rate threshold per hour (default: 50)
ERROR_RATE_THRESHOLD=50
```

### Notification Settings

```env
# Enable email notifications for monitoring (default: true)
MONITORING_EMAIL_ENABLED=true

# Send only critical alerts via email (default: true)
MONITORING_EMAIL_CRITICAL_ONLY=true

# Enable Slack notifications (default: false)
MONITORING_SLACK_ENABLED=false
MONITORING_SLACK_WEBHOOK=https://hooks.slack.com/services/...
MONITORING_SLACK_CHANNEL=#alerts

# Enable SMS notifications (default: false)
MONITORING_SMS_ENABLED=false
SMS_SERVICE=twilio
```

### Metrics Collection

```env
# Metrics retention in days (default: 30)
METRICS_RETENTION_DAYS=30

# Metrics collection interval in minutes (default: 15)
METRICS_INTERVAL=15

# Enable/disable specific metrics collection
COLLECT_ORDER_METRICS=true
COLLECT_PAYMENT_METRICS=true
COLLECT_USER_METRICS=true
COLLECT_INVENTORY_METRICS=true
COLLECT_PERFORMANCE_METRICS=true
```

## Stock Management Variables

```env
# Stock reservation timeout in minutes (default: 15)
STOCK_RESERVATION_TIMEOUT=15
```

## Rate Limiting Variables

```env
# API rate limit per minute (default: 60)
API_RATE_LIMIT=60

# Payment API rate limit per minute (default: 10)
PAYMENT_RATE_LIMIT=10

# Login rate limit per minute (default: 5)
LOGIN_RATE_LIMIT=5
```

## Performance Variables

```env
# Web response time threshold in milliseconds (default: 2000)
WEB_RESPONSE_THRESHOLD=2000

# API response time threshold in milliseconds (default: 1000)
API_RESPONSE_THRESHOLD=1000

# Admin response time threshold in milliseconds (default: 3000)
ADMIN_RESPONSE_THRESHOLD=3000

# Memory usage threshold in MB (default: 512)
MEMORY_THRESHOLD=512

# CPU usage threshold percentage (default: 80)
CPU_THRESHOLD=80

# Enable slow query logging (default: true)
SLOW_QUERY_LOGGING=true
```

## Cleanup Configuration

```env
# Enable automatic cleanup (default: true)
MONITORING_CLEANUP_ENABLED=true

# Cleanup schedule (cron expression, default: daily at 2 AM)
MONITORING_CLEANUP_SCHEDULE="0 2 * * *"

# Data retention periods in days
ALERT_RETENTION_DAYS=30
METRICS_RETENTION_DAYS=90
LOG_RETENTION_DAYS=14
FAILED_JOBS_RETENTION_DAYS=7
SESSION_RETENTION_DAYS=1
```

## Dashboard Configuration

```env
# Enable monitoring dashboard (default: true)
MONITORING_DASHBOARD_ENABLED=true

# Dashboard refresh interval in seconds (default: 30)
DASHBOARD_REFRESH_INTERVAL=30

# Number of recent alerts to show (default: 10)
DASHBOARD_ALERTS_COUNT=10

# Number of recent metrics to show (default: 20)
DASHBOARD_METRICS_COUNT=20

# Enable real-time updates (default: true)
DASHBOARD_REAL_TIME=true
```

## Development Variables

These variables are typically used only in development:

```env
# Enable debug mode (never use in production)
APP_DEBUG=true

# Log deprecation warnings
LOG_DEPRECATIONS_CHANNEL=null
LOG_DEPRECATIONS_TRACE=false

# Vite configuration for asset building
VITE_APP_NAME="${APP_NAME}"
```

## Production Security Checklist

When deploying to production, ensure:

- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] `SESSION_SECURE_COOKIE=true` (if using HTTPS)
- [ ] Strong database passwords
- [ ] Live Stripe keys (not test keys)
- [ ] Proper webhook secrets
- [ ] Admin email configured for alerts
- [ ] Log levels set appropriately (`error` or `warning`)
- [ ] Cache and session drivers configured for performance
- [ ] Monitoring thresholds adjusted for your infrastructure

## Environment File Security

### File Permissions

```bash
# Secure the .env file
chmod 600 .env
chown www-data:www-data .env
```

### Version Control

- Never commit `.env` files to version control
- Use `.env.example` as a template
- Document all required variables
- Use different keys for different environments

### Key Rotation

Regularly rotate sensitive keys:

- Application keys
- Database passwords
- Stripe keys
- Webhook secrets
- API tokens

### Backup Strategy

- Backup `.env` files securely
- Store backups encrypted
- Document recovery procedures
- Test restoration process

## Troubleshooting

### Common Issues

#### Missing Required Variables
```bash
# Check for missing variables
php artisan config:clear
php artisan config:cache
```

#### Invalid Stripe Keys
- Verify keys match your environment (test vs live)
- Check key format and completeness
- Ensure webhook secret is correct

#### Database Connection Issues
- Verify database credentials
- Check database server accessibility
- Ensure database exists and user has permissions

#### Cache Issues
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Validation Commands

```bash
# Test database connection
php artisan tinker
>>> DB::connection()->getPdo()

# Test Stripe configuration
php artisan tinker
>>> config('stripe.key')
>>> config('stripe.secret')

# Validate environment
php artisan config:show
```

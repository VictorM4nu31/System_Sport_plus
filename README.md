# E-Commerce System

A secure Laravel-based e-commerce platform with Stripe payment integration, advanced stock management, and comprehensive security features.

## Features

- **Secure Payment Processing**: Stripe integration with webhook support
- **Advanced Stock Management**: Real-time inventory tracking with reservation system
- **Multi-Address Support**: Users can manage multiple shipping addresses
- **Enhanced Security**: Role-based access control with comprehensive logging
- **Order Management**: Unified order processing with atomic transactions
- **Admin Dashboard**: Complete product and order management

## Quick Start

### Prerequisites

- PHP 8.1 or higher
- Composer
- MySQL 5.7 or higher
- Node.js and npm
- Stripe account for payment processing

### Installation

1. Clone the repository and install dependencies:
```bash
composer install
npm install
```

2. Copy environment file and configure:
```bash
cp .env.example .env
php artisan key:generate
```

3. Configure your database and Stripe settings in `.env` (see Environment Variables section)

4. Run migrations and seed data:
```bash
php artisan migrate
php artisan db:seed
```

5. Build frontend assets:
```bash
npm run build
```

6. Start the development server:
```bash
php artisan serve
```

## Environment Variables

### Required Variables

The following environment variables are **required** for the system to function properly:

#### Database Configuration
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

#### Stripe Configuration (Required)
```env
STRIPE_KEY=pk_test_your_publishable_key_here
STRIPE_SECRET=sk_test_your_secret_key_here
STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret_here
```

#### Application Settings
```env
APP_NAME="Your Store Name"
APP_ENV=production
APP_KEY=base64:your_generated_key_here
APP_DEBUG=false
APP_URL=https://yourdomain.com
```

### Optional Variables

#### Currency Settings
```env
CASHIER_CURRENCY=usd
CASHIER_CURRENCY_LOCALE=en
```

#### Logging Configuration
```env
LOG_CHANNEL=stack
STRIPE_LOGGER=payments
```

#### Session Configuration
```env
SESSION_DRIVER=database
SESSION_LIFETIME=120
```

For a complete list of environment variables, see the `.env.example` file.

## System Architecture

### Core Services

- **StripePaymentService**: Handles all payment processing with Stripe
- **OrderProcessingService**: Manages unified order creation and validation
- **StockManagementService**: Controls inventory with reservation system
- **SecurityLoggingService**: Comprehensive security event logging

### Key Features

#### Payment Processing
- Stripe Elements integration for secure card processing
- Webhook handling for payment confirmations
- Automatic refund processing
- PCI DSS compliant payment handling

#### Stock Management
- Real-time inventory tracking
- Temporary stock reservations during checkout
- Automatic reservation cleanup
- Concurrent order handling

#### Security
- Role-based access control
- Comprehensive audit logging
- Input sanitization middleware
- CSRF protection on all forms

## API Documentation

### Payment Endpoints

#### Create Payment Intent
```http
POST /api/payments/intent
Content-Type: application/json

{
    "amount": 2999,
    "currency": "usd",
    "order_items": [...]
}
```

#### Confirm Payment
```http
POST /api/payments/confirm
Content-Type: application/json

{
    "payment_intent_id": "pi_xxx",
    "payment_method_id": "pm_xxx"
}
```

### Order Endpoints

#### Create Order
```http
POST /api/orders
Content-Type: application/json

{
    "items": [...],
    "shipping_address_id": 1,
    "payment_intent_id": "pi_xxx"
}
```

#### Get Order Status
```http
GET /api/orders/{id}
```

For complete API documentation, see the [API Documentation](docs/api.md) file.

## Deployment

### Production Checklist

1. **Environment Configuration**
   - Set `APP_ENV=production`
   - Set `APP_DEBUG=false`
   - Configure production database
   - Set up Stripe production keys

2. **Security Setup**
   - Configure HTTPS
   - Set up proper file permissions
   - Configure firewall rules
   - Set up SSL certificates

3. **Performance Optimization**
   - Run `php artisan config:cache`
   - Run `php artisan route:cache`
   - Run `php artisan view:cache`
   - Configure Redis for caching (optional)

4. **Monitoring Setup**
   - Configure log rotation
   - Set up error monitoring
   - Configure payment alerts
   - Set up stock monitoring

### Stripe Webhook Configuration

1. In your Stripe dashboard, create a webhook endpoint pointing to:
   ```
   https://yourdomain.com/stripe/webhook
   ```

2. Select the following events:
   - `payment_intent.succeeded`
   - `payment_intent.payment_failed`
   - `charge.dispute.created`

3. Copy the webhook secret to your `STRIPE_WEBHOOK_SECRET` environment variable

## Troubleshooting

### Common Issues

#### Payment Failures
- Check Stripe keys are correctly configured
- Verify webhook endpoint is accessible
- Check payment logs: `storage/logs/payments.log`

#### Stock Issues
- Check database connections
- Verify stock reservation cleanup job is running
- Review stock logs for conflicts

#### Authentication Issues
- Clear application cache: `php artisan cache:clear`
- Check user roles and permissions
- Review security logs: `storage/logs/security.log`

### Log Files

- **Application Logs**: `storage/logs/laravel.log`
- **Payment Logs**: `storage/logs/payments.log`
- **Order Logs**: `storage/logs/orders.log`
- **Security Logs**: `storage/logs/security.log`

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Submit a pull request

## Support

For technical support or questions:
- Check the troubleshooting section above
- Review log files for error details
- Contact the development team

## Security

If you discover a security vulnerability, please send an email to the security team. All security vulnerabilities will be promptly addressed.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

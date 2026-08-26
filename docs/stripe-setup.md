# Stripe Configuration Guide

This guide walks you through setting up Stripe payment processing for the e-commerce system.

## Prerequisites

- A Stripe account (sign up at [stripe.com](https://stripe.com))
- Access to your Stripe Dashboard
- Laravel application with Stripe package installed

## Step 1: Get Your Stripe Keys

1. Log in to your [Stripe Dashboard](https://dashboard.stripe.com)
2. Navigate to **Developers** → **API keys**
3. Copy your **Publishable key** and **Secret key**

### Test vs Live Keys

- **Test keys** (start with `pk_test_` and `sk_test_`): Use for development and testing
- **Live keys** (start with `pk_live_` and `sk_live_`): Use for production only

## Step 2: Configure Environment Variables

Add the following to your `.env` file:

```env
# Stripe Configuration
STRIPE_KEY=pk_test_your_publishable_key_here
STRIPE_SECRET=sk_test_your_secret_key_here
STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret_here

# Optional: Currency settings
CASHIER_CURRENCY=usd
CASHIER_CURRENCY_LOCALE=en
```

### Important Security Notes

- **Never commit real keys to version control**
- Use test keys for development
- Store production keys securely
- Rotate keys regularly

## Step 3: Set Up Webhooks

Webhooks allow Stripe to notify your application about payment events.

### Create Webhook Endpoint

1. In Stripe Dashboard, go to **Developers** → **Webhooks**
2. Click **Add endpoint**
3. Set the endpoint URL to: `https://yourdomain.com/stripe/webhook`
4. Select the following events:
   - `payment_intent.succeeded`
   - `payment_intent.payment_failed`
   - `charge.dispute.created`
   - `invoice.payment_succeeded` (if using subscriptions)
   - `invoice.payment_failed` (if using subscriptions)

### Get Webhook Secret

1. After creating the webhook, click on it
2. In the **Signing secret** section, click **Reveal**
3. Copy the secret (starts with `whsec_`)
4. Add it to your `.env` file as `STRIPE_WEBHOOK_SECRET`

## Step 4: Test Your Configuration

### Test Payment Processing

1. Use Stripe's test card numbers:
   - **Successful payment**: `4242424242424242`
   - **Declined payment**: `4000000000000002`
   - **Requires authentication**: `4000002500003155`

2. Test the checkout flow in your application

### Test Webhooks

1. Use Stripe CLI to forward webhooks to your local development:
   ```bash
   stripe listen --forward-to localhost:8000/stripe/webhook
   ```

2. Trigger test events:
   ```bash
   stripe trigger payment_intent.succeeded
   ```

## Step 5: Production Setup

### Switch to Live Mode

1. Get your live keys from Stripe Dashboard
2. Update your production `.env` file:
   ```env
   STRIPE_KEY=pk_live_your_live_publishable_key
   STRIPE_SECRET=sk_live_your_live_secret_key
   ```

3. Create a new webhook endpoint for production
4. Update `STRIPE_WEBHOOK_SECRET` with the live webhook secret

### Security Checklist

- [ ] Live keys are stored securely
- [ ] Webhook endpoint uses HTTPS
- [ ] Application is in production mode (`APP_ENV=production`)
- [ ] Debug mode is disabled (`APP_DEBUG=false`)
- [ ] Proper error handling is in place

## Configuration Options

### Currency Settings

The system supports multiple currencies. Configure in your `.env`:

```env
CASHIER_CURRENCY=eur  # or usd, gbp, etc.
CASHIER_CURRENCY_LOCALE=de  # for proper formatting
```

### Payment Methods

Enable additional payment methods in `config/stripe.php`:

```php
'payment_methods' => [
    'card',
    'ideal',        // Netherlands
    'sepa_debit',   // Europe
    'bancontact',   // Belgium
    'giropay',      // Germany
],
```

### Webhook Tolerance

Configure webhook timestamp tolerance:

```env
STRIPE_WEBHOOK_TOLERANCE=300  # seconds (default: 5 minutes)
```

## Troubleshooting

### Common Issues

#### Invalid API Key
- **Error**: "No such token"
- **Solution**: Check that your keys are correct and match your environment (test/live)

#### Webhook Signature Verification Failed
- **Error**: "Invalid signature"
- **Solution**: Verify your webhook secret is correct and matches the endpoint

#### Payment Intent Not Found
- **Error**: "No such payment_intent"
- **Solution**: Ensure you're using the correct Stripe account and environment

### Debug Mode

Enable detailed logging by setting:

```env
LOG_LEVEL=debug
STRIPE_LOGGER=payments
```

Check logs in `storage/logs/payments.log` for detailed error information.

### Test Webhook Locally

Use ngrok to expose your local server:

```bash
# Install ngrok
npm install -g ngrok

# Expose local server
ngrok http 8000

# Use the HTTPS URL for webhook endpoint
```

## Support Resources

- [Stripe Documentation](https://stripe.com/docs)
- [Laravel Cashier Documentation](https://laravel.com/docs/billing)
- [Stripe API Reference](https://stripe.com/docs/api)
- [Webhook Testing Guide](https://stripe.com/docs/webhooks/test)

## Security Best Practices

1. **Key Management**
   - Use environment variables for all keys
   - Never hardcode keys in source code
   - Rotate keys regularly
   - Use different keys for different environments

2. **Webhook Security**
   - Always verify webhook signatures
   - Use HTTPS for webhook endpoints
   - Implement idempotency for webhook handlers
   - Log all webhook events for audit

3. **Payment Security**
   - Never store card details on your servers
   - Use Stripe Elements for card input
   - Implement proper error handling
   - Log all payment attempts for monitoring

4. **Monitoring**
   - Set up alerts for failed payments
   - Monitor webhook delivery
   - Track payment success rates
   - Review security logs regularly

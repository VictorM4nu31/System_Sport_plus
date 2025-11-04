# API Documentation

This document describes the API endpoints and services available in the e-commerce system.

## Authentication

Most endpoints require authentication. Include the session token or use Laravel Sanctum tokens.

```http
Authorization: Bearer your-token-here
```

## Payment API

### Create Payment Intent

Creates a new Stripe payment intent for processing payments.

**Endpoint:** `POST /api/payments/intent`

**Request Body:**
```json
{
    "amount": 2999,
    "currency": "usd",
    "order_items": [
        {
            "product_id": 1,
            "quantity": 2,
            "price": 1499
        }
    ],
    "shipping_address_id": 1
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "payment_intent_id": "pi_1234567890",
        "client_secret": "pi_1234567890_secret_abc123",
        "amount": 2999,
        "currency": "usd"
    }
}
```

**Error Response:**
```json
{
    "success": false,
    "message": "Insufficient stock for product ID 1",
    "errors": {
        "stock": ["Product out of stock"]
    }
}
```

### Confirm Payment

Confirms a payment intent after successful payment method collection.

**Endpoint:** `POST /api/payments/confirm`

**Request Body:**
```json
{
    "payment_intent_id": "pi_1234567890",
    "payment_method_id": "pm_1234567890"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "payment_status": "succeeded",
        "order_id": 123,
        "confirmation_number": "ORD-2024-001"
    }
}
```

### Process Refund

Processes a refund for a completed payment.

**Endpoint:** `POST /api/payments/refund`

**Request Body:**
```json
{
    "payment_intent_id": "pi_1234567890",
    "amount": 1000,
    "reason": "requested_by_customer"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "refund_id": "re_1234567890",
        "amount": 1000,
        "status": "succeeded"
    }
}
```

## Order API

### Create Order

Creates a new order with the specified items and shipping information.

**Endpoint:** `POST /api/orders`

**Request Body:**
```json
{
    "items": [
        {
            "product_id": 1,
            "quantity": 2,
            "price": 1499
        }
    ],
    "shipping_address_id": 1,
    "payment_intent_id": "pi_1234567890",
    "notes": "Please handle with care"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "order_id": 123,
        "total_price": 2999,
        "status": "pending",
        "payment_status": "pending",
        "confirmation_number": "ORD-2024-001",
        "estimated_delivery": "2024-01-15"
    }
}
```

### Get Order Details

Retrieves detailed information about a specific order.

**Endpoint:** `GET /api/orders/{id}`

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 123,
        "confirmation_number": "ORD-2024-001",
        "status": "processing",
        "payment_status": "paid",
        "total_price": 2999,
        "created_at": "2024-01-10T10:00:00Z",
        "items": [
            {
                "product_id": 1,
                "product_name": "Sample Product",
                "quantity": 2,
                "unit_price": 1499,
                "total_price": 2998
            }
        ],
        "shipping_address": {
            "full_name": "John Doe",
            "street": "123 Main St",
            "city": "Anytown",
            "state": "CA",
            "postal_code": "12345"
        }
    }
}
```

### Update Order Status

Updates the status of an existing order (admin only).

**Endpoint:** `PATCH /api/orders/{id}/status`

**Request Body:**
```json
{
    "status": "shipped",
    "tracking_number": "1Z999AA1234567890"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "order_id": 123,
        "status": "shipped",
        "tracking_number": "1Z999AA1234567890",
        "updated_at": "2024-01-12T14:30:00Z"
    }
}
```

## Stock Management API

### Check Stock Availability

Checks if sufficient stock is available for the requested items.

**Endpoint:** `POST /api/stock/check`

**Request Body:**
```json
{
    "items": [
        {
            "product_id": 1,
            "quantity": 5
        },
        {
            "product_id": 2,
            "quantity": 2
        }
    ]
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "available": true,
        "items": [
            {
                "product_id": 1,
                "requested": 5,
                "available": 10,
                "sufficient": true
            },
            {
                "product_id": 2,
                "requested": 2,
                "available": 1,
                "sufficient": false
            }
        ]
    }
}
```

### Reserve Stock

Temporarily reserves stock for a specific user during checkout.

**Endpoint:** `POST /api/stock/reserve`

**Request Body:**
```json
{
    "items": [
        {
            "product_id": 1,
            "quantity": 2
        }
    ],
    "expires_in": 900
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "reservation_id": "res_1234567890",
        "expires_at": "2024-01-10T10:15:00Z",
        "items": [
            {
                "product_id": 1,
                "quantity": 2,
                "reserved": true
            }
        ]
    }
}
```

### Release Stock Reservation

Releases a previously made stock reservation.

**Endpoint:** `DELETE /api/stock/reservations/{reservation_id}`

**Response:**
```json
{
    "success": true,
    "message": "Stock reservation released successfully"
}
```

## Address Management API

### Get User Addresses

Retrieves all addresses for the authenticated user.

**Endpoint:** `GET /api/addresses`

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "full_name": "John Doe",
            "street": "123 Main St",
            "city": "Anytown",
            "state": "CA",
            "postal_code": "12345",
            "is_default": true,
            "address_type": "shipping"
        }
    ]
}
```

### Create Address

Creates a new address for the authenticated user.

**Endpoint:** `POST /api/addresses`

**Request Body:**
```json
{
    "full_name": "Jane Doe",
    "street": "456 Oak Ave",
    "city": "Another City",
    "state": "NY",
    "postal_code": "67890",
    "contact_phone": "+1234567890",
    "is_default": false,
    "address_type": "shipping"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 2,
        "full_name": "Jane Doe",
        "street": "456 Oak Ave",
        "city": "Another City",
        "state": "NY",
        "postal_code": "67890",
        "contact_phone": "+1234567890",
        "is_default": false,
        "address_type": "shipping",
        "created_at": "2024-01-10T10:00:00Z"
    }
}
```

### Update Address

Updates an existing address.

**Endpoint:** `PUT /api/addresses/{id}`

**Request Body:**
```json
{
    "full_name": "Jane Smith",
    "street": "789 Pine St",
    "is_default": true
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 2,
        "full_name": "Jane Smith",
        "street": "789 Pine St",
        "city": "Another City",
        "state": "NY",
        "postal_code": "67890",
        "is_default": true,
        "updated_at": "2024-01-10T11:00:00Z"
    }
}
```

### Delete Address

Deletes an address (cannot delete default address if it's the only one).

**Endpoint:** `DELETE /api/addresses/{id}`

**Response:**
```json
{
    "success": true,
    "message": "Address deleted successfully"
}
```

## Product API

### Get Products

Retrieves a paginated list of products.

**Endpoint:** `GET /api/products`

**Query Parameters:**
- `page`: Page number (default: 1)
- `per_page`: Items per page (default: 15, max: 100)
- `category`: Filter by category ID
- `search`: Search in product name and description
- `sort`: Sort by (name, price, created_at)
- `order`: Sort order (asc, desc)

**Response:**
```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "name": "Sample Product",
                "description": "Product description",
                "price": 1499,
                "formatted_price": "$14.99",
                "stock": 10,
                "category": "Electronics",
                "image_url": "/storage/products/sample.jpg",
                "in_stock": true
            }
        ],
        "total": 50,
        "per_page": 15,
        "last_page": 4
    }
}
```

### Get Product Details

Retrieves detailed information about a specific product.

**Endpoint:** `GET /api/products/{id}`

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "Sample Product",
        "description": "Detailed product description",
        "price": 1499,
        "formatted_price": "$14.99",
        "stock": 10,
        "category": {
            "id": 1,
            "name": "Electronics"
        },
        "images": [
            "/storage/products/sample-1.jpg",
            "/storage/products/sample-2.jpg"
        ],
        "specifications": {
            "weight": "1.5 kg",
            "dimensions": "20x15x5 cm"
        },
        "in_stock": true,
        "created_at": "2024-01-01T00:00:00Z"
    }
}
```

## Error Handling

All API endpoints follow a consistent error response format:

```json
{
    "success": false,
    "message": "Error description",
    "errors": {
        "field_name": [
            "Specific error message"
        ]
    },
    "error_code": "VALIDATION_ERROR"
}
```

### Common Error Codes

- `VALIDATION_ERROR`: Input validation failed
- `AUTHENTICATION_ERROR`: User not authenticated
- `AUTHORIZATION_ERROR`: User not authorized for this action
- `NOT_FOUND`: Resource not found
- `STOCK_ERROR`: Insufficient stock
- `PAYMENT_ERROR`: Payment processing failed
- `SERVER_ERROR`: Internal server error

### HTTP Status Codes

- `200`: Success
- `201`: Created
- `400`: Bad Request
- `401`: Unauthorized
- `403`: Forbidden
- `404`: Not Found
- `422`: Unprocessable Entity (validation errors)
- `500`: Internal Server Error

## Rate Limiting

API endpoints are rate limited to prevent abuse:

- **Authenticated users**: 60 requests per minute
- **Guest users**: 30 requests per minute
- **Payment endpoints**: 10 requests per minute

Rate limit headers are included in responses:

```http
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
X-RateLimit-Reset: 1641811200
```

## Webhooks

### Stripe Webhooks

The system handles the following Stripe webhook events:

#### payment_intent.succeeded
Triggered when a payment is successfully processed.

**Payload Example:**
```json
{
    "id": "evt_1234567890",
    "type": "payment_intent.succeeded",
    "data": {
        "object": {
            "id": "pi_1234567890",
            "amount": 2999,
            "currency": "usd",
            "status": "succeeded"
        }
    }
}
```

#### payment_intent.payment_failed
Triggered when a payment fails.

**Payload Example:**
```json
{
    "id": "evt_1234567890",
    "type": "payment_intent.payment_failed",
    "data": {
        "object": {
            "id": "pi_1234567890",
            "amount": 2999,
            "currency": "usd",
            "status": "requires_payment_method",
            "last_payment_error": {
                "code": "card_declined",
                "message": "Your card was declined."
            }
        }
    }
}
```

### Webhook Security

All webhooks are verified using Stripe's signature verification to ensure they come from Stripe. The webhook endpoint is:

```
POST /stripe/webhook
```

## Testing

### Test Data

Use the following test data for development:

**Test Credit Cards:**
- Success: `4242424242424242`
- Declined: `4000000000000002`
- Requires Authentication: `4000002500003155`

**Test Users:**
- Admin: `admin@example.com` / `password`
- Customer: `customer@example.com` / `password`

### API Testing

Use tools like Postman or curl to test API endpoints:

```bash
# Test product listing
curl -X GET "http://localhost:8000/api/products" \
  -H "Accept: application/json"

# Test order creation
curl -X POST "http://localhost:8000/api/orders" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your-token" \
  -d '{
    "items": [{"product_id": 1, "quantity": 1, "price": 1499}],
    "shipping_address_id": 1,
    "payment_intent_id": "pi_test_123"
  }'
```

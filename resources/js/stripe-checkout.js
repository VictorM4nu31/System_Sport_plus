/**
 * Stripe Checkout Integration
 * Handles payment processing with Stripe Elements
 */

class StripeCheckout {
    constructor() {
        this.stripe = null;
        this.elements = null;
        this.cardElement = null;
        this.isProcessing = false;

        this.init();
    }

    async init() {
        // Initialize Stripe with publishable key
        const stripeKey = document.querySelector('meta[name="stripe-key"]')?.getAttribute('content');

        if (!stripeKey) {
            console.error('Stripe publishable key not found');
            return;
        }

        this.stripe = Stripe(stripeKey);
        this.setupEventListeners();
    }

    setupEventListeners() {
        const checkoutButton = document.getElementById('stripe-checkout-button');
        if (checkoutButton) {
            checkoutButton.addEventListener('click', (e) => this.handleCheckout(e));
        }
    }

    async handleCheckout(event) {
        event.preventDefault();

        if (this.isProcessing) {
            return;
        }

        this.isProcessing = true;
        this.updateButtonState(true);

        try {
            // Get cart data
            const cartData = this.getCartData();

            if (!cartData || cartData.length === 0) {
                throw new Error('El carrito está vacío');
            }

            // Create payment intent on the server
            const { clientSecret, orderId } = await this.createPaymentIntent(cartData);

            // Confirm payment with Stripe
            const { error, paymentIntent } = await this.stripe.confirmCardPayment(clientSecret, {
                payment_method: {
                    card: this.cardElement,
                    billing_details: {
                        name: document.querySelector('input[name="billing_name"]')?.value || '',
                        email: document.querySelector('input[name="billing_email"]')?.value || '',
                    }
                }
            });

            if (error) {
                throw new Error(error.message);
            }

            if (paymentIntent.status === 'succeeded') {
                // Payment successful, redirect to order confirmation
                await this.confirmOrder(orderId, paymentIntent.id);
                window.location.href = '/usuario/orders/history';
            }

        } catch (error) {
            this.showError(error.message);
        } finally {
            this.isProcessing = false;
            this.updateButtonState(false);
        }
    }

    async createPaymentIntent(cartData) {
        const response = await fetch('/usuario/cart/create-payment-intent', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                cart: cartData,
                shipping_address_id: document.querySelector('select[name="shipping_address_id"]')?.value
            })
        });

        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || 'Error al crear la intención de pago');
        }

        return await response.json();
    }

    async confirmOrder(orderId, paymentIntentId) {
        const response = await fetch('/usuario/cart/confirm-order', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                order_id: orderId,
                payment_intent_id: paymentIntentId
            })
        });

        if (!response.ok) {
            throw new Error('Error al confirmar la orden');
        }

        return await response.json();
    }

    getCartData() {
        // Get cart data from the page (this will be populated by Blade template)
        const cartDataElement = document.getElementById('cart-data');
        if (cartDataElement) {
            return JSON.parse(cartDataElement.textContent);
        }
        return null;
    }

    updateButtonState(processing) {
        const button = document.getElementById('stripe-checkout-button');
        if (button) {
            button.disabled = processing;
            button.textContent = processing ? 'Procesando...' : 'Proceder al Pago con Stripe';
        }
    }

    showError(message) {
        // Remove existing error messages
        const existingError = document.getElementById('stripe-error-message');
        if (existingError) {
            existingError.remove();
        }

        // Create and show new error message
        const errorDiv = document.createElement('div');
        errorDiv.id = 'stripe-error-message';
        errorDiv.className = 'mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg';
        errorDiv.textContent = message;

        const container = document.getElementById('stripe-payment-container');
        if (container) {
            container.appendChild(errorDiv);
        }

        // Auto-hide error after 5 seconds
        setTimeout(() => {
            if (errorDiv.parentNode) {
                errorDiv.remove();
            }
        }, 5000);
    }

    showSuccess(message) {
        const successDiv = document.createElement('div');
        successDiv.className = 'mt-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg';
        successDiv.textContent = message;

        const container = document.getElementById('stripe-payment-container');
        if (container) {
            container.appendChild(successDiv);
        }
    }
}

// Initialize Stripe checkout when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('stripe-checkout-button')) {
        new StripeCheckout();
    }
});

// Export for use in other modules if needed
window.StripeCheckout = StripeCheckout;

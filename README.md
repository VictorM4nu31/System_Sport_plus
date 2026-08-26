# System Sport Plus — E-Commerce

Plataforma e-commerce de artículos deportivos construida con Laravel 13, con pagos reales vía Stripe, control de inventario con reservas y tres perfiles de usuario (cliente / trabajador / administrador).

## Destacados

- **Pago seguro con Stripe**: checkout con Stripe Elements, webhooks firmados y confirmación server-side del pago.
- **Integridad de precios**: el total a cobrar **se recalcul siempre en el servidor** a partir de los precios reales en BD; nunca se confía en el monto enviado por el cliente.
- **Inventario con reservas**: el stock disponible es `stock real − reservas activas`; las reservas se confirman al cobrar y se liberan al caducar o fallar el pago.
- **Tres roles**: `usuario` (compras, carrito, wishlist, reseñas, direcciones), `trabajador` (aceptar/rechazar y gestionar pedidos) y `administrador` (productos, categorías, pedidos, reportes, monitoreo).
- **Seguridad**: control de acceso basado en roles (Spatie), auditoría con `Log::channel('audit')` de acciones sensibles, sanitización de inputs, atomicidad transaccional con `DB::transaction` y `lockForUpdate()`.
- **Monitoreo**: panel de salud del sistema, métricas y alertas (admin).

## Roles de acceso y demo

Tras ejecutar los seeders se crea el rol `administrador` y un usuario admin:

| Perfil | Cómo obtener acceso |
| --- | --- |
| **Administrador** | `admin@example.com` / `password` (creado por `db:seed`) |
| **Cliente (usuario)** | Registrarse desde `/register` (el rol `usuario` se asigna automáticamente) |
| **Trabajador** | Crear un usuario y asignarle el rol `trabajador` |

### Probar el pago

Usa la tarjeta de prueba de Stripe en el checkout:

```
Número: 4242 4242 4242 4242
Fecha: cualquier fecha futura · CVC: cualquier valor · Nombre: cualquier nombre
```

> El checkout usa claves de Stripe en modo test (`pk_test_...` / `sk_test_...`). En local, los webhooks deben apuntar a `https://<dominio>/stripe/webhook` (usa `stripe listen` para desarrollo local).

## Requisitos

- PHP 8.2 o superior
- Composer
- MySQL 8.0 o superior
- Node.js 18+ y npm
- Cuenta de Stripe (para pagos)

## Instalación

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
# Configura DB y Stripe en .env (ver sección Variables de entorno)
php artisan migrate --seed
npm run build
php artisan serve
```

## Variables de entorno

```env
APP_NAME="System Sport Plus"
APP_ENV=local
APP_URL=http://localhost
APP_KEY=base64:tu_key_generada

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=system_sport_plus
DB_USERNAME=root
DB_PASSWORD=

STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...

CASHIER_CURRENCY=mxn
CASHIER_CURRENCY_LOCALE=es_MX
```

La lista completa está en `.env.example`. En producción, define `STRIPE_AUTO_SYNC=true` solo si deseas sincronizar productos con Stripe al crearlos/actualizarlos (el sync falla de forma silenciosa sin romper el guardado local).

## Arquitectura

Separación por contratos y servicios inyectados:

```
app/Contracts/        (interfaces de Payment / OrderProcessing / StockManagement)
app/Services/         (implementaciones: Stripe, ordenes, inventario, totales)
app/Enums/            (OrderStatus, PaymentStatus)
app/Http/Controllers/ (admin / user / auth / webhook)
app/Models/           (Product, Order, OrderItem, StockReservation, Address, ...)
```

El flujo de pago está centralizado:

1. `CartController::createPaymentIntent` reconstruye el carrito desde BD, **recalcula el total** (subtotal + envío) y reserva stock.
2. Se crea un `PaymentIntent` con el monto calculado en el servidor.
3. El webhook de Stripe (`StripePaymentService::handleWebhook`) confirma el pago, **confirma las reservas de stock** y marca la orden como pagada (idempotente).
4. Si el pago falla o se cancela, se liberan las reservas y la orden se marca como fallida.

## Rutas principales

| Ruta | Descripción |
| --- | --- |
| `/` | Catálogo / landing |
| `/productos` | Listado de productos (cliente) |
| `/carrito` | Carrito y checkout |
| `/carrito/create-payment-intent` | Crea el intento de pago |
| `/carrito/confirm-order` | Confirma la orden tras el cobro |
| `/stripe/webhook` | Webhook de Stripe (sin auth) |
| `/admin/*` | Panel de administración |
| `/trabajador/*` | Gestión de pedidos del trabajador |

Consulta `php artisan route:list` para el listado completo.

## Pruebas

```bash
php artisan test --compact
```

La suite cubre checkout (integridad del total en el servidor), gestión de stock (reservar/confirmar/liberar/expirados) y la regla de envío. El servicio de pago se mockea para no depender de la red.

## Despliegue

> **Importante:** Laravel necesita PHP + MySQL. Vercel y Netlify no ejecutan flujos PHP ni bases relacionales; para una demo en vivo usa **Render** (app web + instancia MySQL), **Laravel Cloud** u otro host con servidor PHP y base de datos.

Pasos mínimos en producción:

1. `APP_ENV=production`, `APP_DEBUG=false`, claves de Stripe de producción y webhook configurado en el dashboard de Stripe apuntando a `/stripe/webhook`.
2. `php artisan migrate --seed` (o migraciones + seeders por separado).
3. `php artisan config:cache && php artisan route:cache && php artisan view:cache`.
4. Conectar los webhooks en Stripe con los eventos `payment_intent.succeeded`, `payment_intent.payment_failed` y `charge.dispute.created`.
5. Programar la limpieza de reservas expiradas: `php artisan stock:release-expired` (cron).

## Licencia

MIT.

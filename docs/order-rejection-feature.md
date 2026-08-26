# Funcionalidad de Rechazo de Pedidos

## Descripción
Esta funcionalidad permite a los trabajadores rechazar pedidos pagados y proporcionar una razón detallada que será visible para el usuario.

## Características Implementadas

### 1. Campos de Base de Datos
- `rejection_reason`: Texto que explica el motivo del rechazo
- `rejected_at`: Timestamp de cuándo se rechazó el pedido
- `rejected_by`: ID del trabajador que rechazó el pedido

### 2. Funcionalidad del Trabajador
- **Vista de Lista**: Botón "Rechazar" junto a "Aceptar" y "Ver"
- **Vista de Detalles**: Botón "Rechazar Pedido" junto a "Aceptar Pedido"
- **Modal de Rechazo**: Formulario para ingresar la razón del rechazo
- **Validación**: Mínimo 10 caracteres, máximo 500 caracteres

### 3. Funcionalidad del Usuario
- **Estado Visual**: Badge "Rechazado" en color rojo
- **Información Detallada**: Sección especial que muestra:
  - Motivo del rechazo
  - Fecha y hora del rechazo
  - Nombre del trabajador que rechazó

### 4. Rutas Agregadas
```php
Route::patch('/{id}/rechazar', 'rejectOrder')->name('orders.reject');
```

### 5. Validaciones
- El pedido debe estar en estado 'paid' o 'pendiente'
- El pedido debe tener payment_status 'paid'
- La razón de rechazo es obligatoria (10-500 caracteres)

### 6. Logging y Auditoría
- Se registra en el log de auditoría cuando un trabajador rechaza un pedido
- Incluye información del trabajador, pedido y razón del rechazo

## Flujo de Uso

### Para el Trabajador:
1. Ve la lista de pedidos pagados pendientes
2. Hace clic en "Rechazar" en cualquier pedido
3. Se abre un modal solicitando la razón del rechazo
4. Ingresa una explicación detallada (mínimo 10 caracteres)
5. Confirma el rechazo
6. El pedido cambia a estado "rejected"

### Para el Usuario:
1. Ve sus pedidos en "Mis Pedidos"
2. Los pedidos rechazados aparecen con badge rojo "Rechazado"
3. Al ver los detalles del pedido, ve una sección especial con:
   - Icono de advertencia
   - Motivo completo del rechazo
   - Fecha y trabajador que lo rechazó

## Estados de Pedido
- `pending`: Pendiente de pago
- `paid`: Pagado, esperando confirmación del trabajador
- `confirmed`: Aceptado por el trabajador
- `rejected`: Rechazado por el trabajador (NUEVO)
- `en proceso`: En proceso de preparación
- `completado`: Completado
- `cancelado`: Cancelado

## Consideraciones de UX
- Los botones están organizados de forma clara (Ver, Aceptar, Rechazar)
- El modal de rechazo es intuitivo y fácil de usar
- La información de rechazo es prominente pero no agresiva
- Se mantiene la consistencia visual con el resto de la aplicación

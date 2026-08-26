<?php

namespace App\Services;

use App\Contracts\StockManagementInterface;
use App\Models\Product;
use App\Models\StockReservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockManagementService implements StockManagementInterface
{
    /**
     * Verificar disponibilidad de stock para un producto
     */
    public function checkAvailability(int $productId, int $quantity): bool
    {
        $availableStock = $this->getAvailableStock($productId);
        return $availableStock >= $quantity;
    }

    /**
     * Reservar stock temporalmente
     */
    public function reserveStock(int $productId, int $quantity, int $userId, int $expirationMinutes = 15): bool
    {
        return DB::transaction(function () use ($productId, $quantity, $userId, $expirationMinutes) {
            // Verificar disponibilidad
            if (!$this->checkAvailability($productId, $quantity)) {
                Log::warning('Intento de reserva fallido: stock insuficiente', [
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'user_id' => $userId,
                    'available_stock' => $this->getAvailableStock($productId)
                ]);
                return false;
            }

            // Verificar si el usuario ya tiene una reserva activa para este producto
            $existingReservation = StockReservation::where('product_id', $productId)
                ->where('user_id', $userId)
                ->active()
                ->first();

            if ($existingReservation) {
                // Actualizar la reserva existente
                $existingReservation->update([
                    'quantity' => $existingReservation->quantity + $quantity,
                    'expires_at' => Carbon::now()->addMinutes($expirationMinutes)
                ]);
            } else {
                // Crear nueva reserva
                StockReservation::create([
                    'product_id' => $productId,
                    'user_id' => $userId,
                    'quantity' => $quantity,
                    'expires_at' => Carbon::now()->addMinutes($expirationMinutes)
                ]);
            }

            Log::info('Stock reservado exitosamente', [
                'product_id' => $productId,
                'quantity' => $quantity,
                'user_id' => $userId,
                'expires_at' => Carbon::now()->addMinutes($expirationMinutes)
            ]);

            return true;
        });
    }

    /**
     * Confirmar una reserva (reducir stock real)
     */
    public function confirmReservation(int $productId, int $quantity, int $userId): bool
    {
        return DB::transaction(function () use ($productId, $quantity, $userId) {
            // Buscar la reserva activa del usuario
            $reservation = StockReservation::where('product_id', $productId)
                ->where('user_id', $userId)
                ->active()
                ->first();

            if (!$reservation || $reservation->quantity < $quantity) {
                Log::error('No se encontró reserva válida para confirmar', [
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'user_id' => $userId
                ]);
                return false;
            }

            // Reducir el stock real del producto
            $product = Product::lockForUpdate()->find($productId);
            if (!$product || $product->stock < $quantity) {
                Log::error('Stock insuficiente en producto para confirmar reserva', [
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'current_stock' => $product ? $product->stock : 0
                ]);
                return false;
            }

            $product->decrement('stock', $quantity);

            // Reducir o eliminar la reserva
            if ($reservation->quantity == $quantity) {
                $reservation->delete();
            } else {
                $reservation->decrement('quantity', $quantity);
            }

            Log::info('Reserva confirmada y stock reducido', [
                'product_id' => $productId,
                'quantity' => $quantity,
                'user_id' => $userId,
                'new_stock' => $product->fresh()->stock
            ]);

            return true;
        });
    }

    /**
     * Liberar una reserva específica
     */
    public function releaseReservation(int $productId, int $quantity, int $userId): bool
    {
        return DB::transaction(function () use ($productId, $quantity, $userId) {
            $reservation = StockReservation::where('product_id', $productId)
                ->where('user_id', $userId)
                ->active()
                ->first();

            if (!$reservation) {
                return false;
            }

            if ($reservation->quantity <= $quantity) {
                $reservation->delete();
            } else {
                $reservation->decrement('quantity', $quantity);
            }

            Log::info('Reserva liberada', [
                'product_id' => $productId,
                'quantity' => $quantity,
                'user_id' => $userId
            ]);

            return true;
        });
    }

    /**
     * Liberar todas las reservas expiradas
     */
    public function releaseExpiredReservations(): int
    {
        $expiredCount = StockReservation::expired()->count();

        if ($expiredCount > 0) {
            StockReservation::expired()->delete();
            Log::info('Reservas expiradas liberadas', ['count' => $expiredCount]);
        }

        return $expiredCount;
    }

    /**
     * Obtener stock disponible (stock real - reservas activas)
     */
    public function getAvailableStock(int $productId): int
    {
        $product = Product::find($productId);
        if (!$product) {
            return 0;
        }

        $reservedStock = StockReservation::where('product_id', $productId)
            ->active()
            ->sum('quantity');

        return max(0, $product->stock - $reservedStock);
    }

    /**
     * Actualizar stock de un producto
     */
    public function updateStock(int $productId, int $newStock): bool
    {
        $product = Product::find($productId);
        if (!$product) {
            return false;
        }

        $product->update(['stock' => $newStock]);

        Log::info('Stock actualizado', [
            'product_id' => $productId,
            'new_stock' => $newStock
        ]);

        return true;
    }

    /**
     * Obtener reservas activas de un usuario
     */
    public function getUserActiveReservations(int $userId): array
    {
        return StockReservation::with('product')
            ->where('user_id', $userId)
            ->active()
            ->get()
            ->toArray();
    }
}

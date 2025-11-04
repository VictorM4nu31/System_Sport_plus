<?php

namespace App\Contracts;

interface StockManagementInterface
{
    /**
     * Verificar disponibilidad de stock para un producto
     */
    public function checkAvailability(int $productId, int $quantity): bool;

    /**
     * Reservar stock temporalmente
     */
    public function reserveStock(int $productId, int $quantity, int $userId, int $expirationMinutes = 15): bool;

    /**
     * Confirmar una reserva (reducir stock real)
     */
    public function confirmReservation(int $productId, int $quantity, int $userId): bool;

    /**
     * Liberar una reserva específica
     */
    public function releaseReservation(int $productId, int $quantity, int $userId): bool;

    /**
     * Liberar todas las reservas expiradas
     */
    public function releaseExpiredReservations(): int;

    /**
     * Obtener stock disponible (stock real - reservas activas)
     */
    public function getAvailableStock(int $productId): int;

    /**
     * Actualizar stock de un producto
     */
    public function updateStock(int $productId, int $newStock): bool;

    /**
     * Obtener reservas activas de un usuario
     */
    public function getUserActiveReservations(int $userId): array;
}

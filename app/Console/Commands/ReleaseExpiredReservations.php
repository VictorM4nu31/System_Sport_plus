<?php

namespace App\Console\Commands;

use App\Contracts\StockManagementInterface;
use Illuminate\Console\Command;

class ReleaseExpiredReservations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:release-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Release expired stock reservations';

    /**
     * Execute the console command.
     */
    public function handle(StockManagementInterface $stockService)
    {
        $this->info('Liberando reservas de stock expiradas...');

        $releasedCount = $stockService->releaseExpiredReservations();

        if ($releasedCount > 0) {
            $this->info("Se liberaron {$releasedCount} reservas expiradas.");
        } else {
            $this->info('No se encontraron reservas expiradas.');
        }

        return Command::SUCCESS;
    }
}

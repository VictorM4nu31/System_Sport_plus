<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncProductsWithStripe extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stripe:sync-products {--force : Force sync even if already synced}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all products with Stripe';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando sincronización de productos con Stripe...');

        $force = $this->option('force');
        $products = $force ?
            \App\Models\Product::all() :
            \App\Models\Product::whereNull('stripe_product_id')->get();

        if ($products->isEmpty()) {
            $this->info('No hay productos para sincronizar.');
            return;
        }

        $this->info("Sincronizando {$products->count()} productos...");

        $stripeService = new \App\Services\StripeProductService();
        $success = 0;
        $errors = 0;

        $bar = $this->output->createProgressBar($products->count());
        $bar->start();

        foreach ($products as $product) {
            $result = $stripeService->syncWithStripe($product);

            if ($result['success']) {
                $success++;
                $this->line("\n✅ Producto sincronizado: {$product->name}");
            } else {
                $errors++;
                $this->line("\n❌ Error en producto {$product->name}: {$result['error']}");
            }

            $bar->advance();
        }

        $bar->finish();

        $this->newLine(2);
        $this->info("Sincronización completada:");
        $this->info("✅ Exitosos: {$success}");
        $this->info("❌ Errores: {$errors}");
    }
}

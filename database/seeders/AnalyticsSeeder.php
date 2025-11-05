<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SiteAnalytics;
use App\Models\ProductView;
use App\Models\Product;
use Carbon\Carbon;

class AnalyticsSeeder extends Seeder
{
    public function run()
    {
        $this->seedSiteAnalytics();
        $this->seedProductViews();
    }

    private function seedSiteAnalytics()
    {
        $pages = [
            '/',
            '/productos',
            '/productos/1',
            '/productos/2',
            '/productos/3',
            '/carrito',
            '/login',
            '/register',
        ];

        $referrers = [
            null, // Directo
            'https://google.com',
            'https://facebook.com',
            'https://instagram.com',
            'https://twitter.com',
        ];

        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 14_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0 Mobile/15E148 Safari/604.1',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (Android 11; Mobile; rv:89.0) Gecko/89.0 Firefox/89.0',
        ];

        // Generar datos para los últimos 30 días
        for ($i = 30; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);

            // Generar entre 10 y 100 visitas por día
            $visitsCount = rand(10, 100);

            for ($j = 0; $j < $visitsCount; $j++) {
                SiteAnalytics::create([
                    'ip_address' => $this->generateRandomIP(),
                    'user_agent' => $userAgents[array_rand($userAgents)],
                    'page_url' => 'https://localhost' . $pages[array_rand($pages)],
                    'referrer' => $referrers[array_rand($referrers)],
                    'user_id' => null, // Solo visitantes anónimos por ahora
                    'session_id' => 'sess_' . uniqid(),
                    'time_on_page' => rand(10, 300),
                    'device_info' => [
                        'is_mobile' => rand(0, 1),
                        'platform' => ['Windows', 'Mac', 'Linux', 'Android', 'iPhone'][array_rand(['Windows', 'Mac', 'Linux', 'Android', 'iPhone'])],
                        'browser' => ['Chrome', 'Firefox', 'Safari', 'Edge'][array_rand(['Chrome', 'Firefox', 'Safari', 'Edge'])],
                    ],
                    'created_at' => $date->addMinutes(rand(0, 1439)), // Distribuir a lo largo del día
                    'updated_at' => $date,
                ]);
            }
        }
    }

    private function seedProductViews()
    {
        $products = Product::all();

        if ($products->isEmpty()) {
            return;
        }

        // Generar vistas de productos para los últimos 30 días
        for ($i = 30; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);

            // Generar entre 5 y 50 vistas de productos por día
            $viewsCount = rand(5, 50);

            for ($j = 0; $j < $viewsCount; $j++) {
                $product = $products->random();

                try {
                    ProductView::create([
                        'product_id' => $product->id,
                        'user_id' => null,
                        'ip_address' => $this->generateRandomIP(),
                        'session_id' => 'sess_' . uniqid(),
                        'created_at' => $date->addMinutes(rand(0, 1439)),
                        'updated_at' => $date,
                    ]);
                } catch (\Exception $e) {
                    // Ignorar duplicados por la restricción unique
                    continue;
                }
            }
        }
    }

    private function generateRandomIP()
    {
        return rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255);
    }
}

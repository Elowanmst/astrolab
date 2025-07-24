<?php

namespace Database\Seeders;

use App\Models\PaymentConfig;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configs = [
            [
                'processor' => 'stripe',
                'name' => 'Stripe',
                'description' => 'Processeur de paiement international par carte bancaire',
                'is_active' => false,
                'is_test_mode' => true,
                'config_data' => [
                    'public_key' => 'pk_test_...',
                    'secret_key' => 'sk_test_...',
                    'webhook_secret' => 'whsec_...',
                ],
                'fee_percentage' => 2.90,
                'fee_fixed' => 0.30,
            ],
            [
                'processor' => 'paypal',
                'name' => 'PayPal',
                'description' => 'Paiement via PayPal et cartes bancaires',
                'is_active' => false,
                'is_test_mode' => true,
                'config_data' => [
                    'client_id' => '',
                    'client_secret' => '',
                    'sandbox' => true,
                ],
                'fee_percentage' => 3.40,
                'fee_fixed' => 0.35,
            ],
            [
                'processor' => 'lyra',
                'name' => 'Lyra/PayZen',
                'description' => 'Solution de paiement française',
                'is_active' => false,
                'is_test_mode' => true,
                'config_data' => [
                    'shop_id' => '',
                    'key_test' => '',
                    'key_prod' => '',
                    'endpoint' => 'https://api.payzen.eu',
                ],
                'fee_percentage' => 2.50,
                'fee_fixed' => 0.25,
            ],
        ];

        foreach ($configs as $config) {
            PaymentConfig::updateOrCreate(
                ['processor' => $config['processor']],
                $config
            );
        }
    }
}

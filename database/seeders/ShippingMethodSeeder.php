<?php

namespace Database\Seeders;

use App\Models\ShippingMethod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShippingMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $methods = [
            [
                'name' => 'Livraison à domicile',
                'code' => 'home',
                'description' => 'Livraison standard à votre domicile par La Poste ou transporteur',
                'price' => 4.90,
                'free_from_amount' => 50.00,
                'estimated_days_min' => 2,
                'estimated_days_max' => 4,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Retrait en magasin',
                'code' => 'pickup',
                'description' => 'Retrait gratuit dans nos locaux - 123 Rue de l\'Espace, Paris',
                'price' => 0.00,
                'free_from_amount' => null,
                'estimated_days_min' => 1,
                'estimated_days_max' => 2,
                'is_active' => true,
                'sort_order' => 0,
            ],
            [
                'name' => 'Livraison express',
                'code' => 'express',
                'description' => 'Livraison express en 24h ouvrées',
                'price' => 9.90,
                'free_from_amount' => 100.00,
                'estimated_days_min' => 1,
                'estimated_days_max' => 1,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Point relais',
                'code' => 'relay',
                'description' => 'Livraison en point relais Mondial Relay',
                'price' => 3.50,
                'free_from_amount' => 75.00,
                'estimated_days_min' => 3,
                'estimated_days_max' => 5,
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($methods as $method) {
            ShippingMethod::updateOrCreate(
                ['code' => $method['code']],
                $method
            );
        }
    }
}

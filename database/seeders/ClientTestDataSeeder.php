<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class ClientTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mettre à jour quelques clients avec des adresses complètes
        $clients = User::where('is_admin', false)->take(3)->get();
        
        foreach ($clients as $index => $client) {
            $client->update([
                'phone' => '0' . rand(1, 9) . rand(10000000, 99999999),
                'billing_address' => ($index + 1) . ' rue de la ' . ['Paix', 'République', 'Liberté'][$index],
                'billing_city' => ['Paris', 'Lyon', 'Marseille'][$index],
                'billing_postal_code' => ['75001', '69001', '13001'][$index],
                'billing_country' => 'France',
                'shipping_address' => ($index + 1) . ' avenue des ' . ['Champs', 'Roses', 'Tulipes'][$index],
                'shipping_city' => ['Paris', 'Lyon', 'Marseille'][$index],
                'shipping_postal_code' => ['75002', '69002', '13002'][$index],
                'shipping_country' => 'France',
                'newsletter_subscribed' => rand(0, 1),
                'email_verified_at' => now()->subDays(rand(1, 30)),
            ]);
        }
        
        echo "✅ Données de test ajoutées pour " . $clients->count() . " clients\n";
    }
}

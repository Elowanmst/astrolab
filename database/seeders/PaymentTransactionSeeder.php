<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PaymentTransaction;
use App\Models\Order;

class PaymentTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer quelques commandes fictives d'abord si elles n'existent pas
        if (Order::count() === 0) {
            echo "⚠️ Aucune commande trouvée. Création de commandes de test...\n";
            
            $orders = [
                [
                    'user_id' => 2, // Sabine
                    'order_number' => 'AST-TEST001',
                    'status' => 'delivered',
                    'payment_status' => 'completed',
                    'total_amount' => 89.90,
                ],
                [
                    'user_id' => 3, // elowan
                    'order_number' => 'AST-TEST002',
                    'status' => 'shipped',
                    'payment_status' => 'completed',
                    'total_amount' => 149.50,
                ],
                [
                    'user_id' => 4, // camelo
                    'order_number' => 'AST-TEST003',
                    'status' => 'processing',
                    'payment_status' => 'completed',
                    'total_amount' => 67.30,
                ],
            ];

            foreach ($orders as $orderData) {
                Order::create($orderData);
            }
        }

        // Créer des transactions de paiement pour les commandes existantes
        $orders = Order::take(5)->get();
        
        foreach ($orders as $order) {
            // Éviter les doublons
            if ($order->paymentTransactions()->count() > 0) {
                continue;
            }

            $processors = ['stripe', 'simulation', 'paypal'];
            $statuses = ['completed', 'completed', 'completed', 'failed']; // 75% de succès
            $processor = $processors[array_rand($processors)];
            $status = $statuses[array_rand($statuses)];

            $fees = match($processor) {
                'stripe' => ($order->total_amount * 1.4 / 100) + 0.25,
                'paypal' => ($order->total_amount * 2.9 / 100) + 0.35,
                default => 0,
            };

            PaymentTransaction::create([
                'order_id' => $order->id,
                'transaction_id' => $processor . '_' . strtoupper(uniqid()),
                'processor' => $processor,
                'status' => $status,
                'amount' => $order->total_amount,
                'fees' => round($fees, 2),
                'currency' => 'EUR',
                'payment_method' => 'card',
                'card_last_4' => str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
                'card_brand' => ['visa', 'mastercard', 'amex'][array_rand(['visa', 'mastercard', 'amex'])],
                'processed_at' => now()->subDays(rand(1, 30)),
                'failure_reason' => $status === 'failed' ? 'Carte expirée' : null,
                'processor_response' => [
                    'status' => $status,
                    'message' => $status === 'completed' ? 'Paiement réussi' : 'Paiement échoué',
                    'created_at' => now()->toISOString(),
                ]
            ]);
        }

        echo "✅ " . PaymentTransaction::count() . " transactions de paiement créées\n";
    }
}

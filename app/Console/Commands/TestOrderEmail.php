<?php

namespace App\Console\Commands;

use App\Mail\OrderConfirmation;
use App\Mail\NewOrderNotification;
use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestOrderEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:order-email {order_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Teste l\'envoi d\'emails de confirmation de commande';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orderId = $this->argument('order_id');
        
        if ($orderId) {
            $order = Order::with('items')->find($orderId);
        } else {
            $order = Order::with('items')->latest()->first();
        }

        if (!$order) {
            $this->error('Aucune commande trouvée.');
            return;
        }

        $this->info("Test d'envoi d'email pour la commande #{$order->order_number}");
        $this->info("Email client: {$order->shipping_email}");

        try {
            // Test email client
            Mail::to($order->shipping_email)->send(new OrderConfirmation($order));
            $this->info("✅ Email de confirmation client envoyé");

            // Test email admin
            $adminEmail = config('mail.admin_email', 'admin@astrolab.com');
            Mail::to($adminEmail)->send(new NewOrderNotification($order));
            $this->info("✅ Email de notification admin envoyé vers: {$adminEmail}");

            if (config('mail.default') === 'log') {
                $this->warn("⚠️  Mode LOG actif - Vérifiez storage/logs/laravel.log pour voir les emails");
            }

        } catch (\Exception $e) {
            $this->error("❌ Erreur: " . $e->getMessage());
        }
    }
}

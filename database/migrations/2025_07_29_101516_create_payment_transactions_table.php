<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('transaction_id')->unique(); // ID de transaction Stripe/PayPal/etc.
            $table->string('processor'); // stripe, paypal, lyra, simulation
            $table->string('status'); // pending, completed, failed, cancelled, refunded
            $table->decimal('amount', 10, 2); // Montant de la transaction
            $table->decimal('fees', 10, 2)->default(0); // Frais de transaction
            $table->string('currency', 3)->default('EUR');
            $table->string('payment_method')->nullable(); // card, bank_transfer, etc.
            $table->string('card_last_4')->nullable(); // 4 derniers chiffres de la carte
            $table->string('card_brand')->nullable(); // visa, mastercard, etc.
            $table->text('processor_response')->nullable(); // Réponse complète du processeur (JSON)
            $table->string('failure_reason')->nullable(); // Raison de l'échec
            $table->timestamp('processed_at')->nullable(); // Date de traitement
            $table->timestamp('refunded_at')->nullable(); // Date de remboursement
            $table->decimal('refunded_amount', 10, 2)->default(0); // Montant remboursé
            $table->timestamps();
            
            // Index pour optimiser les recherches
            $table->index(['processor', 'status']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};

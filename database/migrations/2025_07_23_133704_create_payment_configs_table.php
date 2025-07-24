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
        Schema::create('payment_configs', function (Blueprint $table) {
            $table->id();
            $table->string('processor'); // stripe, paypal, lyra, etc.
            $table->string('name'); // Nom d'affichage
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(false);
            $table->boolean('is_test_mode')->default(true);
            $table->json('config_data'); // Configuration spécifique au processeur
            $table->decimal('fee_percentage', 5, 2)->default(0); // % de commission
            $table->decimal('fee_fixed', 8, 2)->default(0); // Frais fixes
            $table->timestamps();
            
            $table->unique(['processor']); // Un seul config par processeur
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_configs');
    }
};

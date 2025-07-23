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
        Schema::create('shipping_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nom de la méthode de livraison
            $table->string('code')->unique(); // Code unique (home, pickup, express, etc.)
            $table->text('description')->nullable(); // Description détaillée
            $table->decimal('price', 8, 2)->default(0); // Prix de la livraison
            $table->decimal('free_from_amount', 8, 2)->nullable(); // Montant à partir duquel c'est gratuit
            $table->integer('estimated_days_min')->nullable(); // Délai minimum en jours
            $table->integer('estimated_days_max')->nullable(); // Délai maximum en jours
            $table->boolean('is_active')->default(true); // Actif/Inactif
            $table->integer('sort_order')->default(0); // Ordre d'affichage
            $table->json('restrictions')->nullable(); // Restrictions (poids, taille, zones, etc.)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_methods');
    }
};

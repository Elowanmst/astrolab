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
        Schema::create('product_size_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('size'); // XXS, XS, S, M, L, XL, XXL
            $table->integer('stock')->default(0);
            $table->timestamps();
            
            // Index pour éviter les doublons produit/taille
            $table->unique(['product_id', 'size']);
            
            // Index pour optimiser les recherches
            $table->index(['product_id', 'stock']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_size_stocks');
    }
};

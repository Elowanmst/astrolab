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
        Schema::table('users', function (Blueprint $table) {
            // Champ admin
            $table->boolean('is_admin')->default(false)->after('email_verified_at');
            
            // Champs de facturation
            $table->string('billing_address')->nullable()->after('country');
            $table->string('billing_city')->nullable()->after('billing_address');
            $table->string('billing_postal_code')->nullable()->after('billing_city');
            $table->string('billing_country')->nullable()->after('billing_postal_code');
            
            // Champs de livraison
            $table->string('shipping_address')->nullable()->after('billing_country');
            $table->string('shipping_city')->nullable()->after('shipping_address');
            $table->string('shipping_postal_code')->nullable()->after('shipping_city');
            $table->string('shipping_country')->nullable()->after('shipping_postal_code');
            
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
        // Vérifier chaque colonne avant de la supprimer
        $columnsToDelete = [];
        
        if (Schema::hasColumn('users', 'is_admin')) {
            $columnsToDelete[] = 'is_admin';
        }
        if (Schema::hasColumn('users', 'billing_address')) {
            $columnsToDelete[] = 'billing_address';
        }
        if (Schema::hasColumn('users', 'billing_city')) {
            $columnsToDelete[] = 'billing_city';
        }
        if (Schema::hasColumn('users', 'billing_postal_code')) {
            $columnsToDelete[] = 'billing_postal_code';
        }
        if (Schema::hasColumn('users', 'billing_country')) {
            $columnsToDelete[] = 'billing_country';
        }
        if (Schema::hasColumn('users', 'shipping_address')) {
            $columnsToDelete[] = 'shipping_address';
        }
        if (Schema::hasColumn('users', 'shipping_city')) {
            $columnsToDelete[] = 'shipping_city';
        }
        if (Schema::hasColumn('users', 'shipping_postal_code')) {
            $columnsToDelete[] = 'shipping_postal_code';
        }
        if (Schema::hasColumn('users', 'shipping_country')) {
            $columnsToDelete[] = 'shipping_country';
        }
        
        // Supprimer uniquement les colonnes qui existent
        if (!empty($columnsToDelete)) {
            $table->dropColumn($columnsToDelete);
        }
    });
    }
};

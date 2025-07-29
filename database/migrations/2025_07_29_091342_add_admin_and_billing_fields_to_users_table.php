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
            
            // Renommer le champ newsletter pour cohérence
            $table->renameColumn('newsletter', 'newsletter_subscribed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'is_admin',
                'billing_address',
                'billing_city', 
                'billing_postal_code',
                'billing_country',
                'shipping_address',
                'shipping_city',
                'shipping_postal_code', 
                'shipping_country'
            ]);
            
            $table->renameColumn('newsletter_subscribed', 'newsletter');
        });
    }
};

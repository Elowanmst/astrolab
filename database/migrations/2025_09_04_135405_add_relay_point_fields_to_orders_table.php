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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_method')->default('home')->after('shipping_postal_code'); // home ou pickup
            $table->string('relay_point_id')->nullable()->after('shipping_method'); // ID du point relais
            $table->string('relay_point_name')->nullable()->after('relay_point_id'); // Nom du point relais
            $table->text('relay_point_address')->nullable()->after('relay_point_name'); // Adresse du point relais
            $table->string('relay_point_postal_code')->nullable()->after('relay_point_address'); // CP du point relais
            $table->string('relay_point_city')->nullable()->after('relay_point_postal_code'); // Ville du point relais
            $table->json('relay_point_data')->nullable()->after('relay_point_city'); // Données complètes du point relais
            $table->string('tracking_number')->nullable()->after('relay_point_data'); // Numéro de suivi Mondial Relay
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_method',
                'relay_point_id',
                'relay_point_name', 
                'relay_point_address',
                'relay_point_postal_code',
                'relay_point_city',
                'relay_point_data',
                'tracking_number'
            ]);
        });
    }
};

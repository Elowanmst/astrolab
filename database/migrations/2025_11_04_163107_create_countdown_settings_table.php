<?php
// filepath: database/migrations/xxxx_xx_xx_xxxxxx_create_countdown_settings_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('countdown_settings', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('LANCEMENT IMMINENT');
            $table->string('subtitle')->default('Préparez-vous pour la nouvelle collection');
            $table->datetime('end_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('countdown_settings');
    }
};
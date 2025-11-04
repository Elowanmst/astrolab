<?php
// filepath: database/migrations/2025_11_04_164500_update_countdown_settings_remove_subtitle.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('countdown_settings', function (Blueprint $table) {
            // Supprimer la colonne subtitle
            $table->dropColumn('subtitle');
            
            // Modifier title pour être nullable
            $table->string('title')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('countdown_settings', function (Blueprint $table) {
            // Remettre la colonne subtitle
            $table->string('subtitle')->default('Préparez-vous pour la nouvelle collection');
            
            // Remettre title comme required
            $table->string('title')->default('LANCEMENT IMMINENT')->change();
        });
    }
};
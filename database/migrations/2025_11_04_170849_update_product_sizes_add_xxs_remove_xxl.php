<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Mettre à jour les produits existants avec la taille XXL vers XL
        DB::table('products')
            ->where('size', 'XXL')
            ->update(['size' => 'XL']);

        // Note: Pas de produits XXS à créer automatiquement
        // Les nouveaux produits XXS seront ajoutés manuellement via l'admin
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // En cas de rollback, remettre quelques produits en XXL
        // (Cette opération est approximative car on ne peut pas retrouver
        // quels produits étaient initialement en XXL)
        DB::table('products')
            ->where('size', 'XL')
            ->limit(5) // Limite arbitraire pour éviter de tout changer
            ->update(['size' => 'XXL']);
    }
};

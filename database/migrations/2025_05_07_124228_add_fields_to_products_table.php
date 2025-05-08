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
        Schema::table('products', function (Blueprint $table) {
            $table->string('size')->nullable()->after('description'); // Taille du vêtement
            $table->string('material')->nullable()->after('size'); // Matériau du vêtement
            $table->enum('gender', ['male', 'female', 'unisex'])->default('unisex')->after('material'); // Genre
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['size', 'material', 'gender']);
        });
    }
};

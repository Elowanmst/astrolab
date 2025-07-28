<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Banner::create([
            'title' => 'COLLECTION STELLAE 01',
            'subtitle' => 'DES ÉDITIONS ÉPHÉMÈRES EXCLUSIVES IMAGINÉES PAR DES ILLUSTRATEURS INDÉPENDANTS',
            'image_path' => null, // Utiliser l'image par défaut
            'is_active' => true,
            'order' => 1,
        ]);
    }
}

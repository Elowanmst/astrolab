<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\HomePageSetting;

class HomePageSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            'hero_title' => 'ASTROLAB',
            'hero_subtitle' => 'DES ÉDITIONS ÉPHÉMÈRES EXCLUSIVES IMAGINÉES PAR DES ILLUSTRATEURS INDÉPENDANTS',
            'hero_image' => null,
            'promotion_image' => null,
        ];

        foreach ($settings as $key => $value) {
            HomePageSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }
}

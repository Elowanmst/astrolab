<?php

namespace Database\Seeders;

use App\Models\Collection;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CollectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Collection::create([
            'name' => 'Collection Stellae 01',
            'description' => 'Découvrez notre première collection exclusive',
            'is_active' => true,
            'order' => 1,
        ]);
    }
}

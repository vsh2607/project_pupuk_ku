<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterFertilizer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MasterFertilizerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MasterFertilizer::create([
            'name' => 'Urea'
        ]);
        MasterFertilizer::create([
            'name' => 'NPK'
        ]);
        MasterFertilizer::create([
            'name' => 'NPK Formula'
        ]);

    }
}

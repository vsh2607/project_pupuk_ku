<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterPlantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
    \App\Models\MasterPlant::create(['name' => 'Kelapa']);
    \App\Models\MasterPlant::create(['name' => 'Padi']);
    \App\Models\MasterPlant::create(['name' => 'Jagung']);
    \App\Models\MasterPlant::create(['name' => 'Tebu']);
    \App\Models\MasterPlant::create(['name' => 'Kopi']);
    \App\Models\MasterPlant::create(['name' => 'Teh']);
    \App\Models\MasterPlant::create(['name' => 'Cengkeh']);
    \App\Models\MasterPlant::create(['name' => 'Lada']);
    \App\Models\MasterPlant::create(['name' => 'Karet']);
    \App\Models\MasterPlant::create(['name' => 'Sawit']);
    \App\Models\MasterPlant::create(['name' => 'Kakao']);
    \App\Models\MasterPlant::create(['name' => 'Kacang Tanah']);
    \App\Models\MasterPlant::create(['name' => 'Kacang Hijau']);
    \App\Models\MasterPlant::create(['name' => 'Kacang Merah']);
    \App\Models\MasterPlant::create(['name' => 'Kacang Panjang']);
    \App\Models\MasterPlant::create(['name' => 'Kacang Kedelai']);
    \App\Models\MasterPlant::create(['name' => 'Kentang']);
    \App\Models\MasterPlant::create(['name' => 'Wortel']);
    \App\Models\MasterPlant::create(['name' => 'Tomat']);
    }
}

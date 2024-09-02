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
    \App\Models\MasterPlant::create(['name' => 'KELAPA']);
    \App\Models\MasterPlant::create(['name' => 'PADI']);
    \App\Models\MasterPlant::create(['name' => 'JAGUNG']);
    \App\Models\MasterPlant::create(['name' => 'TEBU']);
    \App\Models\MasterPlant::create(['name' => 'KOPI']);
    \App\Models\MasterPlant::create(['name' => 'TEH']);
    \App\Models\MasterPlant::create(['name' => 'CENGKEH']);
    \App\Models\MasterPlant::create(['name' => 'LADA']);
    \App\Models\MasterPlant::create(['name' => 'KARET']);
    \App\Models\MasterPlant::create(['name' => 'SAWIT']);
    \App\Models\MasterPlant::create(['name' => 'KAKAO']);
    \App\Models\MasterPlant::create(['name' => 'KACANG TANAH']);
    \App\Models\MasterPlant::create(['name' => 'KACANG HIJAU']);
    \App\Models\MasterPlant::create(['name' => 'KACANG MERAH']);
    \App\Models\MasterPlant::create(['name' => 'KACANG PANJANG']);
    \App\Models\MasterPlant::create(['name' => 'KACANG KEDELAI']);
    \App\Models\MasterPlant::create(['name' => 'KENTANG']);
    \App\Models\MasterPlant::create(['name' => 'WORTEL']);
    \App\Models\MasterPlant::create(['name' => 'TOMAT']);
    }
}

<?php

namespace Database\Seeders;

use App\Models\MasterMenu;
use App\Models\MasterUser;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(
            [
                MasterUserSeeder::class,
                MasterUserTypeSeeder::class,
                MasterMenuSeeder::class,
                MasterPlantSeeder::class,
                MasterFarmerPlantSeeder::class,
                MasterFarmerSeeder::class,
                MasterFertilizerSeeder::class,
            ]
        );
    }
}

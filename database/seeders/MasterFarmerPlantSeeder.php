<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MasterFarmerPlant;


class MasterFarmerPlantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $data = [
            [
                'id' => 1,
                'id_master_farmer' => 1,
                'id_master_plant' => 1,
                'created_at' => '2024-09-04 02:54:43',
                'updated_at' => '2024-09-04 02:54:43',
                'deleted_at' => null
            ],
            [
                'id' => 2,
                'id_master_farmer' => 1,
                'id_master_plant' => 2,
                'created_at' => '2024-09-04 02:54:43',
                'updated_at' => '2024-09-04 02:54:43',
                'deleted_at' => null
            ],
            [
                'id' => 3,
                'id_master_farmer' => 1,
                'id_master_plant' => 3,
                'created_at' => '2024-09-04 02:54:43',
                'updated_at' => '2024-09-04 02:54:43',
                'deleted_at' => null
            ],
            [
                'id' => 4,
                'id_master_farmer' => 2,
                'id_master_plant' => 1,
                'created_at' => '2024-09-04 02:55:52',
                'updated_at' => '2024-09-04 02:55:52',
                'deleted_at' => null
            ],
            [
                'id' => 5,
                'id_master_farmer' => 2,
                'id_master_plant' => 8,
                'created_at' => '2024-09-04 02:55:52',
                'updated_at' => '2024-09-04 02:55:52',
                'deleted_at' => null
            ],
            [
                'id' => 6,
                'id_master_farmer' => 3,
                'id_master_plant' => 12,
                'created_at' => '2024-09-04 02:56:56',
                'updated_at' => '2024-09-04 02:56:56',
                'deleted_at' => null
            ],
            [
                'id' => 7,
                'id_master_farmer' => 3,
                'id_master_plant' => 19,
                'created_at' => '2024-09-04 02:56:56',
                'updated_at' => '2024-09-04 02:56:56',
                'deleted_at' => null
            ],
            [
                'id' => 8,
                'id_master_farmer' => 4,
                'id_master_plant' => 7,
                'created_at' => '2024-09-04 03:03:23',
                'updated_at' => '2024-09-04 03:03:23',
                'deleted_at' => null
            ],
            [
                'id' => 9,
                'id_master_farmer' => 4,
                'id_master_plant' => 16,
                'created_at' => '2024-09-04 03:03:23',
                'updated_at' => '2024-09-04 03:03:23',
                'deleted_at' => null
            ],
            [
                'id' => 10,
                'id_master_farmer' => 5,
                'id_master_plant' => 2,
                'created_at' => '2024-09-04 03:28:14',
                'updated_at' => '2024-09-04 03:28:14',
                'deleted_at' => null
            ],
            [
                'id' => 11,
                'id_master_farmer' => 5,
                'id_master_plant' => 18,
                'created_at' => '2024-09-04 03:28:14',
                'updated_at' => '2024-09-04 03:28:14',
                'deleted_at' => null
            ],
            [
                'id' => 12,
                'id_master_farmer' => 5,
                'id_master_plant' => 11,
                'created_at' => '2024-09-04 03:28:14',
                'updated_at' => '2024-09-04 03:28:14',
                'deleted_at' => null
            ],
            [
                'id' => 13,
                'id_master_farmer' => 6,
                'id_master_plant' => 3,
                'created_at' => '2024-09-04 03:28:59',
                'updated_at' => '2024-09-04 03:28:59',
                'deleted_at' => null
            ],
            [
                'id' => 14,
                'id_master_farmer' => 6,
                'id_master_plant' => 10,
                'created_at' => '2024-09-04 03:28:59',
                'updated_at' => '2024-09-04 03:28:59',
                'deleted_at' => null
            ],
            [
                'id' => 15,
                'id_master_farmer' => 7,
                'id_master_plant' => 5,
                'created_at' => '2024-09-04 03:34:43',
                'updated_at' => '2024-09-04 03:34:43',
                'deleted_at' => null
            ],
        ];

        MasterFarmerPlant::insert($data);
    }
}

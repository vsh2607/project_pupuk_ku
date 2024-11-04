<?php

namespace App\Imports;

use App\Models\MasterFarmer;
use App\Models\MasterFarmerFertilizer;
use App\Models\MasterFarmerPlant;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MasterFarmerImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
       foreach($rows as $row){
            $masterFarmer = MasterFarmer::create([
                "id" => $row["farmer_id"],
                "name" => $row["farmer_name"],
                "land_type" => $row["land_type"],
                "handphone_number" => $row["phone_number"],
                "land_area" => $row["farmer_land_area"],
                "land_location" => $row["land_location"],
            ]);

            for($i = 0; $i < 3; $i++){
                MasterFarmerPlant::create([
                    "id_master_farmer" => $masterFarmer->id,
                    "id_master_plant" => rand(1, 8),
                ]);
            }

            MasterFarmerFertilizer::create([
                "id_master_farmer" => $masterFarmer->id,
                "id_master_fertilizer" => 1,
                "quantity_owned" => $row["jumlah_fertilizer_1"],
            ]);
            MasterFarmerFertilizer::create([
                "id_master_farmer" => $masterFarmer->id,
                "id_master_fertilizer" => 2,
                "quantity_owned" => $row["jumlah_fertilizer_2"],
            ]);


       }
    }
}

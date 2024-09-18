<?php

namespace Database\Seeders;

use App\Models\MasterFarmer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterFarmerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'id' => 1,
                'name' => 'VALENTINO',
                'land_type' => 'OWNED',
                'handphone_number' => '08112643522',
                'land_area' => '100',
                'land_location' => '-7.775027856725384,110.44947473578365,-7.775027856725384,110.45002225822908,-7.776069615499647,110.450065201166,-7.776122766388157,110.44944252858099',
                'fertilizer_quantity_owned' => '200.00',
                'fertilizer_quantity_needed' => '20.00',
                'created_at' => '2024-09-04 02:54:43',
                'updated_at' => '2024-09-04 02:54:43',
                'deleted_at' => null
            ],
            [
                'id' => 2,
                'name' => 'SAS',
                'land_type' => 'OWNED',
                'handphone_number' => '08775562848',
                'land_area' => '100',
                'land_location' => '-7.775131355874308,110.45032662600653,-7.775242972982008,110.4507936304453,-7.775721331678752,110.45078289471108,-7.775827633537295,110.45052523708968,-7.776215635092454,110.45047155841856,-7.776141223863108,110.45006360051802,-7.775062259554652,110.44996161104291',
                'fertilizer_quantity_owned' => '153.00',
                'fertilizer_quantity_needed' => '10.00',
                'created_at' => '2024-09-04 02:55:52',
                'updated_at' => '2024-09-04 02:55:52',
                'deleted_at' => null
            ],
            [
                'id' => 3,
                'name' => 'HENRY',
                'land_type' => 'LEASED',
                'handphone_number' => '087775562848',
                'land_area' => '12',
                'land_location' => '-7.775730560425158,110.45061667349508,-7.775754478345148,110.45102999926273,-7.776296617499345,110.45100315992714,-7.776206261022294,110.4504878446844',
                'fertilizer_quantity_owned' => '150.00',
                'fertilizer_quantity_needed' => '200.00',
                'created_at' => '2024-09-04 02:56:56',
                'updated_at' => '2024-09-04 02:56:56',
                'deleted_at' => null
            ],
            [
                'id' => 4,
                'name' => 'HANI',
                'land_type' => 'LEASED',
                'handphone_number' => '0895612178734',
                'land_area' => '98',
                'land_location' => '-7.776274101049109,110.45097722438807,-7.776337882083452,110.45201322274069,-7.774886861153672,110.4519702798038,-7.774971902811782,110.4510523745276',
                'fertilizer_quantity_owned' => '50.00',
                'fertilizer_quantity_needed' => '500.00',
                'created_at' => '2024-09-04 03:03:23',
                'updated_at' => '2024-09-04 03:03:23',
                'deleted_at' => null
            ],
            [
                'id' => 5,
                'name' => 'ELSARI',
                'land_type' => 'LEASED',
                'handphone_number' => '08777123123',
                'land_area' => '300',
                'land_location' => '-7.776072127709756,110.44952649304217,-7.776295361394972,110.45104559943488,-7.776709937923809,110.45098655289668,-7.776619581535763,110.4495479645106',
                'fertilizer_quantity_owned' => '145.00',
                'fertilizer_quantity_needed' => '5.00',
                'created_at' => '2024-09-04 03:28:14',
                'updated_at' => '2024-09-04 03:28:14',
                'deleted_at' => null
            ],
            [
                'id' => 6,
                'name' => 'DIMAS',
                'land_type' => 'OWNED',
                'handphone_number' => '098123123',
                'land_area' => '20',
                'land_location' => '-7.776305991567483,110.45107873788983,-7.776343197169205,110.45205032183712,-7.776534540211599,110.45203421823578,-7.776534540211599,110.45100358775022',
                'fertilizer_quantity_owned' => '15.00',
                'fertilizer_quantity_needed' => '5.00',
                'created_at' => '2024-09-04 03:28:59',
                'updated_at' => '2024-09-04 03:28:59',
                'deleted_at' => null
            ],
            [
                'id' => 7,
                'name' => 'SETIADY',
                'land_type' => 'LEASED',
                'handphone_number' => '0123123',
                'land_area' => '9',
                'land_location' => '-7.774164006363898,110.45096107594863,-7.774264993504933,110.4516642665403,-7.774727407998261,110.45158911640075,-7.774621105860986,110.45088055794194',
                'fertilizer_quantity_owned' => '10.00',
                'fertilizer_quantity_needed' => '100.00',
                'created_at' => '2024-09-04 03:34:43',
                'updated_at' => '2024-09-04 03:34:43',
                'deleted_at' => null
            ],
        ];

        MasterFarmer::insert($data);
    }
}

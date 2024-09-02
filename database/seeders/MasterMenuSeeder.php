<?php

namespace Database\Seeders;

use App\Models\MasterMenu;
use Illuminate\Database\Seeder;

class MasterMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MasterMenu::create([
            'level' => 0,
            'title' => ' Dashboard',
            'code' => 'dashboard',
            'is_dropdown' => 0,
            'is_hidden' => 0,
            'priority' => 0,
            'icon' => 'fa fa-chart-pie'
        ]);
        MasterMenu::create([
            'level' => 0,
            'title' => ' Master Data',
            'code' => 'master-data',
            'is_dropdown' => 1,
            'is_hidden' => 0,
            'priority' => 1,
            'icon' => 'fa fa-sitemap'
        ]);


        MasterMenu::create([
            'level' => 2,
            'title' => ' Master Petani',
            'code' => 'master-farmer',
            'is_dropdown' => 0,
            'is_hidden' => 0,
            'priority' => 1,
            'icon' => ''
        ]);
        MasterMenu::create([
            'level' => 2,
            'title' => ' Master Tanaman',
            'code' => 'master-plant',
            'is_dropdown' => 0,
            'is_hidden' => 0,
            'priority' => 0,
            'icon' => ''
        ]);

        //Id : 5
        MasterMenu::create([
            'level' => 0,
            'title' => ' Module Manajemen',
            'code' => 'module-management',
            'is_dropdown' => 1,
            'is_hidden' => 0,
            'priority' => 2,
            'icon' => 'fa fa-cogs'
        ]);

        MasterMenu::create([
            'level' => 5,
            'title' => ' Distribusi Pupuk',
            'code' => 'fertilizer-distribution',
            'is_dropdown' => 0,
            'is_hidden' => 0,
            'priority' => 0,
            'icon' => ''
        ]);


    }
}

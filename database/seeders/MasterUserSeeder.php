<?php

namespace Database\Seeders;

use App\Models\MasterUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MasterUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MasterUser::create([
            'name' => 'Admin',
            'username' => 'admin',
            'user_type' => 'ADMIN',
            'password' => Hash::make('admin123')
        ]);
    }
}

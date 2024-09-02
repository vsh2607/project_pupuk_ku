<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterFarmer extends Model
{
    use HasFactory, SoftDeletes;


    protected $fillable = [
        'name',
        'land_type',
        'handphone_number',
        'land_area',
        'land_location',
        'fertilizer_quantity_owned',
        'fertilizer_quantity_needed',
        'created_at',
        'updated_at',
    ];


    public function farmerPlants(){
        return $this->hasMany(MasterFarmerPlant::class, 'id_master_farmer', 'id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class THFarmerPlanned extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'th_farmer_planned';

    protected $fillable = [
        'id_master_farmer',
        'planned_date',
        'land_area',
        'status'
    ];

    public function MasterFarmer()
    {
        return $this->belongsTo(MasterFarmer::class, 'id_master_farmer', 'id');
    }

    public function TDFarmerPlanned(){
        return $this->hasMany(TDFarmerPlanned::class, 'id_th_farmer_planned', 'id');
    }

    public function TDFarmerPlantPlanned(){
        return $this->hasMany(TDFarmerPlantPlanned::class, 'id_th_farmer_planned', 'id');
    }
}

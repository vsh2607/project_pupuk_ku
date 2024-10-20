<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TDFarmerPlantPlanned extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'td_farmer_plant_planned';

    protected $fillable = [
        'id_th_farmer_planned',
        'id_master_plant'
    ];

    public function MasterPlant(){
        return $this->belongsTo(MasterPlant::class, 'id_master_plant', 'id');
    }


}

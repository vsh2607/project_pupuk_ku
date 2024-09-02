<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterFarmerPlant extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'master_farmer_plants';

    protected $fillable = ['id_master_farmer', 'id_master_plant'];

    public function plant(){
        return $this->belongsTo(MasterPlant::class, 'id_master_plant', 'id');
    }
}

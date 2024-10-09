<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterFarmerFertilizer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'master_farmer_fertilizers';

    protected $fillable = [
        'id_master_farmer',
        'id_master_fertilizer',
        'quantity_owned',
        'quantity_needed'
    ];

    public function MasterFertilizer(){
        return $this->belongsTo(MasterFertilizer::class, 'id_master_fertilizer', 'id');
    }
}

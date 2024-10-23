<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TDFarmerPlanned extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'td_farmer_planned';

    protected $fillable = [
        'id_th_farmer_planned',
        'id_master_fertilizer',
        'quantity_planned',
        'quantity_owned'
    ];

    public function MasterFertilizer(){
        return $this->belongsTo(MasterFertilizer::class, 'id_master_fertilizer', 'id');
    }
}

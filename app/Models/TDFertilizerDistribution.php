<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TDFertilizerDistribution extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id_th_fertilizer_distribution',
        'id_farmer_borrower',
        'id_farmer_lender',
        'id_th_farmer_planned',
        'id_master_fertilizer',
        'total_loan',
        'total_return'
    ];
    protected $table = 'td_fertilizer_distribution';

    public function farmerBorrower(){
        return $this->belongsTo(MasterFarmer::class, 'id_farmer_borrower', 'id');
    }

    public function farmerLender(){
        return $this->belongsTo(MasterFarmer::class, 'id_farmer_lender', 'id');
    }

    public function THFarmerPlanned(){
        return $this->belongsTo(THFarmerPlanned::class, 'id_th_farmer_planned', 'id');
    }

    public function MasterFertilizer(){
        return $this->belongsTo(MasterFertilizer::class, 'id_master_fertilizer', 'id');
    }
}

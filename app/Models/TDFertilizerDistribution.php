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
}

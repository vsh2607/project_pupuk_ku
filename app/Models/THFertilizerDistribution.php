<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class THFertilizerDistribution extends Model
{

    use HasFactory, SoftDeletes;
    protected $table = 'th_fertilizer_distribution';
    protected $fillable = ['periode', 'periode_date'];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KpiGrade extends Model
{
    protected $fillable = ['grade_name', 'weight_percentage'];

    public function payrollDistributions(): HasMany
    {
        return $this->hasMany(PayrollDistribution::class);
    }
}

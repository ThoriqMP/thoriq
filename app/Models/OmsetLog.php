<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OmsetLog extends Model
{
    protected $fillable = [
        'tanggal',
        'nominal_omset',
        'tahun',
        'alokasi_gaji',
        'alokasi_perusahaan',
        'alokasi_development',
        'alokasi_partnership',
        'alokasi_penasehat',
        'alokasi_saham',
        'gaji_pokok_pool',
        'tukin_pool',
        'sales_id',
        'status'
    ];

    public function sales(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sales_id');
    }

    public function payrollDistributions(): HasMany
    {
        return $this->hasMany(PayrollDistribution::class);
    }
}

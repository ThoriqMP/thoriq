<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollDistribution extends Model
{
    protected $fillable = [
        'omset_log_id',
        'user_id',
        'kpi_grade_id',
        'nominal_gapok_diterima',
        'nominal_tukin_diterima',
        'status_pembayaran'
    ];

    public function omsetLog(): BelongsTo
    {
        return $this->belongsTo(OmsetLog::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kpiGrade(): BelongsTo
    {
        return $this->belongsTo(KpiGrade::class);
    }
}

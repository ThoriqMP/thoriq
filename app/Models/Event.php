<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $fillable = [
        'nama_event',
        'tanggal_event',
        'pic_id',
        'budget_transportasi',
        'budget_akomodasi',
        'budget_venue',
        'total_budget'
    ];

    protected $casts = [
        'tanggal_event' => 'date'
    ];

    public function pic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pic_id');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(EventExpense::class);
    }
}

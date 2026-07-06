<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventExpense extends Model
{
    protected $fillable = [
        'event_id',
        'kategori',
        'nama_item',
        'quantity',
        'harga_satuan',
        'total_harga',
        'tanggal_pengeluaran',
        'catatan'
    ];

    protected $casts = [
        'tanggal_pengeluaran' => 'date'
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}

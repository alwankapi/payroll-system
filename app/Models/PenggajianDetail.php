<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenggajianDetail extends Model
{
    protected $table = 'penggajian_detail';

    protected $fillable = [
        'penggajian_id',
        'potongan_id',
        'nama_potongan',
        'nilai_potongan',
    ];

    protected $casts = [
        'nilai_potongan' => 'decimal:2',
    ];

    public function penggajian(): BelongsTo
    {
        return $this->belongsTo(Penggajian::class);
    }

    public function potongan(): BelongsTo
    {
        return $this->belongsTo(Potongan::class);
    }
}
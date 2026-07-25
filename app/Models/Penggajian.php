<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Penggajian extends Model
{
    protected $fillable = [
        'karyawan_id',
        'periode',
        'gaji_pokok',
        'tunjangan',
        'total_potongan',
        'gaji_bersih',
        'status',
        'tanggal_bayar',
    ];

    protected $casts = [
        'periode' => 'date',
        'tanggal_bayar' => 'date',
        'gaji_pokok' => 'decimal:2',
        'tunjangan' => 'decimal:2',
        'total_potongan' => 'decimal:2',
        'gaji_bersih' => 'decimal:2',
    ];

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(PenggajianDetail::class);
    }

    /**
     * Get all potongans for this penggajian through details
     * This is used in SlipGajiService and views
     */
    public function potongans()
    {
        return $this->hasManyThrough(
            Potongan::class,
            PenggajianDetail::class,
            'penggajian_id', // Foreign key on penggajian_detail table
            'id',            // Foreign key on potongans table
            'id',            // Local key on penggajians table
            'potongan_id'    // Local key on penggajian_detail table
        );
    }

    protected function isFinal(): Attribute
    {
        return Attribute::make(
            get: fn () => in_array($this->status, ['final', 'dibayar']),
        );
    }

    protected function isTerkunci(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status !== 'draft',
        );
    }

    protected function periodeLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->periode?->translatedFormat('F Y'),
        );
    }
}
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
        'catatan',
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
            get: fn () => in_array($this->status, ['disetujui', 'dibayar']),
        );
    }

    protected function isTerkunci(): Attribute
    {
        return Attribute::make(
            get: fn () => !in_array($this->status, ['draft', 'ditolak', 'dibatalkan']),
        );
    }

    protected function statusBadgeClass(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->status) {
                'draft' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                'diproses' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                'disetujui' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                'dibayar' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-300',
                'ditolak' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                'dibatalkan' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
                default => 'bg-gray-100 text-gray-800',
            },
        );
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->status) {
                'draft' => 'Draft',
                'diproses' => 'Diproses',
                'disetujui' => 'Disetujui',
                'dibayar' => 'Dibayar',
                'ditolak' => 'Ditolak',
                'dibatalkan' => 'Dibatalkan',
                default => ucfirst($this->status),
            },
        );
    }

    protected function periodeLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->periode?->translatedFormat('F Y'),
        );
    }
}
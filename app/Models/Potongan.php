<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Potongan extends Model
{
    protected $fillable = [
        'nama_potongan',
        'jenis_potongan',
        'nilai',
        'status_aktif',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
        'status_aktif' => 'boolean',
    ];

    public function penggajianDetails(): HasMany
    {
        return $this->hasMany(PenggajianDetail::class);
    }

    protected function isPersentase(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->jenis_potongan === 'persentase',
        );
    }

    public function hitungPotongan(float $gajiPokok): float
    {
        return $this->jenis_potongan === 'persentase'
            ? $gajiPokok * (float) $this->nilai / 100
            : (float) $this->nilai;
    }
}
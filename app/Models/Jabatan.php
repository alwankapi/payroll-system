<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jabatan extends Model
{
    protected $fillable = [
        'nama_jabatan',
        'gaji_pokok',
        'tunjangan_jabatan',
        'keterangan',
    ];

    protected $casts = [
        'gaji_pokok' => 'decimal:2',
        'tunjangan_jabatan' => 'decimal:2',
    ];

    public function karyawans(): HasMany
    {
        return $this->hasMany(Karyawan::class);
    }

    protected function totalKompensasi(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => $this->gaji_pokok + $this->tunjangan_jabatan,
        );
    }
}
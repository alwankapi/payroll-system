<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Karyawan extends Model
{
    protected $fillable = [
        'user_id',
        'jabatan_id',
        'nik',
        'nama_lengkap',
        'alamat',
        'no_telepon',
        'tanggal_masuk',
        'no_rekening',
        'status_karyawan',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jabatan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function penggajians(): HasMany
    {
        return $this->hasMany(Penggajian::class);
    }

    protected function isAktif(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status_karyawan === 'aktif',
        );
    }

    protected function nik(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => strtoupper(trim($value)),
        );
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RekapAbsensi extends Model
{
    protected $fillable = [
        'karyawan_id',
        'bulan',
        'tahun',
        'total_hari_kerja',
        'hadir',
        'izin',
        'sakit',
        'alpha',
        'terlambat',
        'lembur',
        'catatan',
    ];

    protected $casts = [
        'bulan' => 'integer',
        'tahun' => 'integer',
        'total_hari_kerja' => 'integer',
        'hadir' => 'integer',
        'izin' => 'integer',
        'sakit' => 'integer',
        'alpha' => 'integer',
        'terlambat' => 'integer',
        'lembur' => 'integer',
    ];

    /**
     * Relasi ke Karyawan
     */
    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }

    /**
     * Accessor: periode display
     */
    public function getPeriodeDisplayAttribute(): string
    {
        $bulanNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        return $bulanNames[$this->bulan] . ' ' . $this->tahun;
    }

    /**
     * Accessor: total kehadiran
     */
    public function getTotalKehadiranAttribute(): int
    {
        return $this->hadir + $this->izin + $this->sakit + $this->alpha;
    }

    /**
     * Scope: filter by periode
     */
    public function scopeByPeriode($query, $bulan = null, $tahun = null)
    {
        if ($bulan) {
            $query->where('bulan', $bulan);
        }
        if ($tahun) {
            $query->where('tahun', $tahun);
        }
        return $query;
    }

    /**
     * Scope: search by karyawan
     */
    public function scopeSearch($query, $search)
    {
        return $query->whereHas('karyawan', function ($q) use ($search) {
            $q->where('nama_lengkap', 'like', "%{$search}%")
              ->orWhere('nik', 'like', "%{$search}%");
        });
    }
}

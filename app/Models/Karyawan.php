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
        'is_active',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'is_active' => 'boolean',
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

    public function kehadirans(): HasMany
    {
        return $this->hasMany(Kehadiran::class);
    }

    public function rekapAbsensis(): HasMany
    {
        return $this->hasMany(RekapAbsensi::class);
    }

    /**
     * Check if employee is active (not magang/intern)
     */
    protected function isAktif(): Attribute
    {
        return Attribute::make(
            get: fn () => in_array($this->status_karyawan, ['tetap', 'kontrak']),
        );
    }

    /**
     * Get status label for display
     */
    public function statusLabel(): string
    {
        return match($this->status_karyawan) {
            'tetap' => 'Tetap',
            'kontrak' => 'Kontrak',
            'magang' => 'Magang',
            default => ucfirst($this->status_karyawan),
        };
    }

    /**
     * Get badge CSS class for status
     */
    public function statusBadgeClass(): string
    {
        // If not active, show red badge regardless of employment type
        if (!$this->is_active) {
            return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
        }

        return match($this->status_karyawan) {
            'tetap' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
            'kontrak' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
            'magang' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300',
        };
    }

    /**
     * Get active status label
     */
    public function activeStatusLabel(): string
    {
        return $this->is_active ? 'Aktif' : 'Nonaktif';
    }

    /**
     * Get combined badge label (employment type + active status)
     */
    public function fullStatusLabel(): string
    {
        if (!$this->is_active) {
            return 'Nonaktif';
        }
        
        return $this->statusLabel();
    }

    /**
     * Check if has payment history (untuk validasi delete)
     */
    public function hasPaymentHistory(): bool
    {
        return $this->penggajians()->exists();
    }

    protected function nik(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => strtoupper(trim($value)),
        );
    }
}

# Fitur Soft Delete Karyawan

## Overview
Fitur untuk menonaktifkan karyawan yang memiliki riwayat penggajian, bukan menghapusnya secara permanent. Ini mencegah kehilangan data historis penggajian.

## Implementasi

### 1. Database Schema
**Migration**: `2026_07_27_180902_add_is_active_to_karyawans_table.php`

Menambahkan kolom `is_active` (boolean):
```php
$table->boolean('is_active')->default(true)->after('status_karyawan');
$table->index('is_active');
```

### 2. Model Changes
**File**: `app/Models/Karyawan.php`

**Fillable & Casts**:
```php
protected $fillable = [
    // ... existing fields
    'is_active',
];

protected $casts = [
    'tanggal_masuk' => 'date',
    'is_active' => 'boolean',
];
```

**Helper Methods**:
```php
// Cek apakah punya riwayat penggajian
public function hasPaymentHistory(): bool

// Label status aktif/nonaktif
public function activeStatusLabel(): string

// Label lengkap (employment type atau "Nonaktif")
public function fullStatusLabel(): string

// Badge class - otomatis merah jika nonaktif
public function statusBadgeClass(): string
```

### 3. Controller Logic
**File**: `app/Http/Controllers/KaryawanController.php`

**Method `destroy()`**:
- **Jika punya riwayat penggajian**: Set `is_active = false` (nonaktifkan)
- **Jika tidak punya riwayat**: Hapus permanent

```php
if ($karyawan->hasPaymentHistory()) {
    $karyawan->update(['is_active' => false]);
    // Success message: "Karyawan berhasil dinonaktifkan..."
} else {
    $karyawan->delete();
    // Success message: "Data karyawan berhasil dihapus."
}
```

### 4. View Changes
**File**: `resources/views/karyawan/index.blade.php`

**Badge Display**:
```blade
<span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $karyawan->statusBadgeClass() }}">
    {{ $karyawan->fullStatusLabel() }}
</span>
```

**Confirm Dialog**:
```javascript
confirm(`... Jika karyawan memiliki riwayat penggajian, sistem akan menonaktifkan karyawan (bukan menghapus).`)
```

## Status Badge Behavior

### Karyawan Aktif (`is_active = true`)
- **Tetap**: Badge hijau - "Tetap"
- **Kontrak**: Badge kuning - "Kontrak"  
- **Magang**: Badge biru - "Magang"

### Karyawan Nonaktif (`is_active = false`)
- **Semua employment types**: Badge merah - "Nonaktif"

## Testing

### Manual Test Commands
```bash
# Test helper methods
php artisan tinker --execute="
\$k = \App\Models\Karyawan::first();
echo 'is_active: ' . (\$k->is_active ? 'true' : 'false') . PHP_EOL;
echo 'fullStatusLabel: ' . \$k->fullStatusLabel() . PHP_EOL;
echo 'statusBadgeClass: ' . \$k->statusBadgeClass() . PHP_EOL;
echo 'hasPaymentHistory: ' . (\$k->hasPaymentHistory() ? 'true' : 'false') . PHP_EOL;
"

# Test deactivation
php artisan tinker --execute="
\$k = \App\Models\Karyawan::first();
\$k->update(['is_active' => false]);
echo 'After deactivation: ' . \$k->fullStatusLabel() . PHP_EOL;
\$k->update(['is_active' => true]); // Reset
"
```

### Browser Testing
1. Login sebagai admin
2. Buka halaman Karyawan (`/karyawan`)
3. Coba hapus karyawan yang **punya** riwayat penggajian
   - ✅ Harus muncul message: "Karyawan berhasil dinonaktifkan..."
   - ✅ Badge berubah jadi merah "Nonaktif"
   - ✅ Data masih ada di database
4. Coba hapus karyawan yang **tidak punya** riwayat penggajian
   - ✅ Harus muncul message: "Data karyawan berhasil dihapus."
   - ✅ Data benar-benar terhapus

## Benefits

1. **Data Integrity**: Riwayat penggajian tetap utuh
2. **Audit Trail**: Karyawan nonaktif masih bisa dilihat
3. **Flexibility**: Karyawan nonaktif bisa diaktifkan kembali jika perlu
4. **User Experience**: Clear feedback tentang apa yang terjadi saat delete

## Future Enhancements

Potensial improvement untuk masa depan:
- [ ] Filter untuk show/hide karyawan nonaktif
- [ ] Tombol "Aktifkan Kembali" di halaman detail
- [ ] Report karyawan nonaktif
- [ ] Prevent login untuk karyawan nonaktif

## Migration Commands

```bash
# Run migration
php artisan migrate

# Rollback (if needed)
php artisan migrate:rollback

# Fresh with seed
php artisan migrate:fresh --seed
```

## Created
- Date: 2026-07-27
- Migration: `2026_07_27_180902_add_is_active_to_karyawans_table.php`

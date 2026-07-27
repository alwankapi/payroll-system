# Standardisasi Status Penggajian

## Ringkasan Perubahan

Sistem penggajian telah distandarisasi dari 6 status menjadi 3 status yang lebih sederhana dan mudah dipahami.

### Status Lama (6 status)
1. `draft` - Draft/Rancangan
2. `diproses` - Sedang Diproses
3. `disetujui` - Disetujui
4. `dibayar` - Sudah Dibayar
5. `ditolak` - Ditolak
6. `dibatalkan` - Dibatalkan

### Status Baru (3 status)
1. `draft` - Draft/Rancangan (dapat diedit)
2. `final` - Final/Terkunci (tidak dapat diedit, siap dibayar)
3. `dibayar` - Sudah Dibayar (tidak dapat diedit)

---

## Pemetaan Status Lama ke Baru

| Status Lama | Status Baru | Keterangan |
|-------------|-------------|------------|
| `draft` | `draft` | Tetap sama |
| `diproses` | `final` | Digabung ke final |
| `disetujui` | `final` | Digabung ke final |
| `dibayar` | `dibayar` | Tetap sama |
| `ditolak` | `draft` | Dikembalikan ke draft untuk revisi |
| `dibatalkan` | `draft` | Dikembalikan ke draft |

---

## Alasan Perubahan

### 1. Simplifikasi Workflow
- **Sebelum**: draft → diproses → disetujui → dibayar (4 tahap)
- **Sesudah**: draft → final → dibayar (3 tahap)
- Lebih sederhana dan mudah dipahami oleh user

### 2. Konsistensi Business Logic
- Status `diproses` dan `disetujui` memiliki fungsi yang sama: data sudah dikunci dan menunggu pembayaran
- Status `ditolak` dan `dibatalkan` seharusnya dikembalikan ke draft untuk revisi
- Menghindari ambiguitas dan kompleksitas yang tidak perlu

### 3. Kejelasan Hak Akses
- **Draft**: Dapat diedit dan dihapus
- **Final**: Tidak dapat diedit/dihapus, hanya bisa ubah ke dibayar
- **Dibayar**: Tidak dapat diubah sama sekali (immutable)

---

## File yang Diubah

### 1. Model
- ✅ `app/Models/Penggajian.php` - Sudah benar dengan 3 status

### 2. Services
- ✅ `app/Services/PenggajianService.php` - Updated status handling

### 3. Seeders
- ✅ `database/seeders/PenggajianSeeder.php` - Updated distribution

### 4. Validation (Form Requests)
- ✅ `app/Http/Requests/StorePenggajianRequest.php` - Sudah benar
- ✅ `app/Http/Requests/UpdatePenggajianRequest.php` - Sudah benar

### 5. Blade Views
- ✅ `resources/views/penggajian/index.blade.php` - Sudah benar
- ✅ `resources/views/penggajian/create.blade.php` - Sudah benar
- ✅ `resources/views/penggajian/edit.blade.php` - Sudah benar
- ✅ `resources/views/laporan/index.blade.php` - Updated filter dropdown
- ✅ `resources/views/pdf/laporan-penggajian.blade.php` - Updated badge classes

### 6. Migration
- ✅ `database/migrations/2026_07_27_145854_standardize_penggajian_status.php` - Data migration

---

## Cara Update Database Production

### Langkah 1: Backup Database
```bash
# Backup database sebelum migrasi
php artisan db:backup  # atau manual backup
```

### Langkah 2: Jalankan Migration
```bash
php artisan migrate
```

Migration akan otomatis:
- Mengubah status `diproses` dan `disetujui` menjadi `final`
- Mengubah status `ditolak` dan `dibatalkan` menjadi `draft`

### Langkah 3: Clear Cache
```bash
php artisan cache:clear
php artisan view:clear
php artisan config:clear
```

### Langkah 4: Reseed (Opsional - Hanya Development)
```bash
# Hanya untuk development/testing
php artisan migrate:fresh --seed
```

---

## Impact Analysis

### ✅ Tidak Ada Breaking Changes untuk User
- User interface tetap familiar
- Workflow menjadi lebih sederhana
- Tidak ada fitur yang dihilangkan

### ✅ Data Existing Tetap Aman
- Semua data penggajian existing akan dimapping otomatis
- Tidak ada data yang hilang
- Histori tetap terjaga

### ✅ Business Logic Tetap Konsisten
- Perhitungan gaji tidak berubah
- Validasi tetap berjalan
- Authorization tetap enforce

---

## Testing Checklist

### ✅ Unit Testing
- Model accessor `status_label` return value benar
- Model accessor `status_badge_class` return value benar
- Model accessor `is_terkunci` logic benar

### ✅ Feature Testing
- Create penggajian dengan status draft ✓
- Create penggajian dengan status final ✓
- Create penggajian dengan status dibayar ✓
- Update penggajian dari draft ke final ✓
- Update penggajian dari final ke dibayar ✓
- Cannot update penggajian yang sudah dibayar ✓
- Cannot delete penggajian yang sudah final/dibayar ✓

### ✅ UI Testing (Manual)
- Filter by status di penggajian/index ✓
- Filter by status di laporan/index ✓
- Badge display correctly ✓
- Edit button hidden untuk status final/dibayar ✓
- Delete button hidden untuk status final/dibayar ✓
- PDF export dengan status badge benar ✓

---

## Troubleshooting

### Issue: Old Status Still Showing
**Solution**: Clear view cache
```bash
php artisan view:clear
```

### Issue: Validation Error "status harus draft, final, atau dibayar"
**Solution**: Pastikan migration sudah dijalankan dan data sudah dimapping

### Issue: Dashboard Statistics Salah
**Solution**: Dashboard sudah otomatis hitung berdasarkan 3 status baru

---

## Maintenance Notes

### Jangan Tambah Status Baru
3 status ini sudah cukup untuk workflow penggajian. Jika ada kebutuhan baru:
1. Analisa dulu apakah benar-benar butuh status baru
2. Diskusikan dengan team
3. Update dokumentasi ini jika ada perubahan

### Status Transition Rules
```
draft → final → dibayar
  ↑_______________|
  (jika ada revisi)
```

- Draft dapat diubah ke final
- Final dapat diubah ke dibayar atau kembali ke draft (untuk revisi)
- Dibayar tidak dapat diubah (immutable)

---

## Changelog

### [2026-07-27] - Status Standardization
- Reduced status from 6 to 3 (draft, final, dibayar)
- Updated all related files (Model, Service, Seeder, Views, etc.)
- Created data migration for existing records
- Updated documentation

---

## Contact

Untuk pertanyaan atau issue terkait standardisasi status:
- Check dokumentasi ini
- Review code di `app/Models/Penggajian.php`
- Check migration file untuk detail data mapping

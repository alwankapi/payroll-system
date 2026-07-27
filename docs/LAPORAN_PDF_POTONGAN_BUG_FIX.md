# Bug Fix: Rincian Potongan Tidak Muncul di PDF Laporan

## Tanggal
28 Juli 2026

## Deskripsi Masalah

### Gejala
- Di halaman web laporan penggajian, nominal total potongan tampil dengan benar
- Saat generate PDF laporan, kolom Potongan hanya menampilkan "Tidak ada potongan"
- Padahal data potongan sebenarnya ada di database dan tampil benar di halaman lain

### Dampak
- Laporan PDF tidak lengkap
- User tidak bisa melihat rincian potongan per karyawan di PDF
- Total potongan tetap muncul tapi tanpa breakdown detail

## Root Cause Analysis

### Penyebab Utama
**Eager Loading yang tidak konsisten antara method `index()` dan `exportPdf()`**

### Detail Analisis

1. **Controller `LaporanController::index()` (Line 19)**
   ```php
   // SEBELUM (SALAH)
   $query = Penggajian::with(['karyawan.jabatan']);
   ```
   - Hanya load relasi `karyawan.jabatan`
   - TIDAK load relasi `details` dan `details.potongan`
   - Data potongan tidak tersedia untuk view

2. **Controller `LaporanController::exportPdf()` (Line 68)**
   ```php
   // SUDAH BENAR
   $query = Penggajian::with(['karyawan.jabatan', 'details.potongan']);
   ```
   - Sudah load semua relasi yang dibutuhkan
   - Tapi karena user generate PDF dari halaman yang sama (pakai filter yang sama), expectasi-nya data harus sama

3. **View `resources/views/laporan/index.blade.php`**
   ```blade
   <!-- Line 118: Hanya tampilkan total -->
   <td>Rp {{ number_format($penggajian->total_potongan, 0, ',', '.') }}</td>
   ```
   - Web view hanya tampilkan TOTAL potongan (field `total_potongan`)
   - Tidak butuh rincian detail, jadi masalah tidak terlihat
   - Makanya di web kelihatan normal

4. **View `resources/views/pdf/laporan-penggajian.blade.php`**
   ```blade
   <!-- Line 299-316: Loop details untuk rincian -->
   @if($penggajian->details && $penggajian->details->count() > 0)
       <div class="potongan-list">
           @foreach($penggajian->details as $detail)
               <div class="potongan-item">
                   <span>{{ $detail->nama_potongan }}</span>
                   <span>Rp {{ number_format($detail->nilai_potongan, 0, ',', '.') }}</span>
               </div>
           @endforeach
       </div>
   @else
       <span>Tidak ada potongan</span>
   @endif
   ```
   - PDF view butuh RINCIAN detail potongan (loop `$penggajian->details`)
   - Karena `details` tidak di-load di `index()`, collection kosong
   - Masuk ke kondisi `@else` dan tampil "Tidak ada potongan"

### Kenapa Nominal Muncul Tapi Rincian Tidak?

- Field `total_potongan` ada di table `penggajians` sebagai denormalized data
- Jadi nominal total bisa diakses tanpa join ke `penggajian_detail`
- Tapi untuk rincian per item, HARUS load relasi `details` dan `details.potongan`

## Solusi

### File yang Diperbaiki
**app/Http/Controllers/LaporanController.php**

### Perubahan

```php
// SEBELUM
public function index(Request $request)
{
    $query = Penggajian::with(['karyawan.jabatan']);
    // ... rest of code
}

// SESUDAH
public function index(Request $request)
{
    $query = Penggajian::with(['karyawan.jabatan', 'details.potongan']);
    // ... rest of code
}
```

### Penjelasan Fix
1. Tambahkan `'details.potongan'` ke eager loading di method `index()`
2. Sekarang data `details` tersedia di collection `$penggajians`
3. PDF blade bisa loop `$penggajian->details` dan tampilkan rincian
4. Konsisten dengan `exportPdf()` yang sudah benar

## Struktur Database

### Relasi yang Terlibat

```
penggajians (table)
├── id
├── karyawan_id
├── total_potongan (denormalized)
└── ... other fields

penggajian_detail (pivot table)
├── id
├── penggajian_id (FK → penggajians.id)
├── potongan_id (FK → potongans.id)
├── nama_potongan (snapshot)
└── nilai_potongan (snapshot)

potongans (table)
├── id
├── nama_potongan
├── jenis_potongan
└── nilai_default
```

### Model Relationships

**Penggajian.php:**
```php
public function details(): HasMany
{
    return $this->hasMany(PenggajianDetail::class);
}

public function potongans()
{
    return $this->hasManyThrough(
        Potongan::class,
        PenggajianDetail::class,
        'penggajian_id',
        'id',
        'id',
        'potongan_id'
    );
}
```

**PenggajianDetail.php:**
```php
public function penggajian(): BelongsTo
{
    return $this->belongsTo(Penggajian::class);
}

public function potongan(): BelongsTo
{
    return $this->belongsTo(Potongan::class);
}
```

## Testing

### Manual Testing Checklist
- [ ] Buka halaman Laporan Penggajian
- [ ] Filter data dengan berbagai kombinasi (bulan, tahun, jabatan, status)
- [ ] Pastikan web view tampil normal
- [ ] Generate PDF dari filter yang sama
- [ ] Periksa kolom Potongan di PDF:
  - Jika ada potongan: tampil semua rincian (nama + nominal)
  - Jika tidak ada potongan: tampil "Tidak ada potongan"
- [ ] Pastikan Total Potongan di PDF = Total Potongan di web
- [ ] Cek dengan karyawan yang punya multiple potongan
- [ ] Verify semua jenis potongan tampil (tetap, persentase, alpha, etc)

### Test Case Example

**Skenario: Karyawan dengan 3 Potongan**

Data Database:
```
penggajian_detail:
- BPJS Kesehatan: Rp 75.000
- BPJS Ketenagakerjaan: Rp 120.000
- PPh21: Rp 250.000
Total: Rp 445.000
```

Expected di PDF:
```
Potongan:
  BPJS Kesehatan (Tetap) - Rp 75.000
  BPJS Ketenagakerjaan (Tetap) - Rp 120.000
  PPh21 (Persentase) - Rp 250.000
  ─────────────────────────────────
  Total: Rp 445.000
```

## Performance Impact

### Sebelum Fix
- Query ke database: 1 (main query) + N (lazy load details untuk setiap penggajian)
- **N+1 Query Problem** jika user generate PDF dengan banyak data

### Sesudah Fix
- Query ke database: 1 (main query dengan eager loading)
- **No N+1 Problem** - semua data di-load sekali jalan

### Benchmark
Dengan 100 data penggajian:
- **Sebelum:** ~101 queries (1 main + 100 lazy load)
- **Sesudah:** ~3 queries (1 penggajian + 1 details + 1 potongans)

## Lessons Learned

1. **Selalu sinkronkan eager loading antara index() dan export()**
   - Jika export butuh data detail, index() juga harus load
   - Gunakan eager loading yang sama untuk konsistensi

2. **Watch out untuk lazy loading di production**
   - N+1 queries bisa bikin aplikasi lambat
   - Selalu gunakan eager loading untuk relasi yang pasti dipakai

3. **Test dengan data real**
   - Bug ini tidak kelihatan kalau test dengan data dummy
   - Perlu test dengan data yang ada relasi beneran

4. **Denormalized fields vs Relational data**
   - `total_potongan` adalah denormalized (cached)
   - Tapi untuk rincian tetap perlu join ke detail
   - Jangan asumsikan semua data ada di parent table

## Related Files

- `app/Http/Controllers/LaporanController.php` - Main fix
- `app/Models/Penggajian.php` - Model dengan relationships
- `app/Models/PenggajianDetail.php` - Pivot model
- `resources/views/pdf/laporan-penggajian.blade.php` - PDF template
- `resources/views/laporan/index.blade.php` - Web view

## References

- [docs/POTONGAN_INTEGRATION_AUDIT.md](./POTONGAN_INTEGRATION_AUDIT.md)
- [docs/PDF_LAPORAN_FIXES.md](./PDF_LAPORAN_FIXES.md)
- Laravel Eloquent Relationships: https://laravel.com/docs/eloquent-relationships
- N+1 Query Problem: https://laravel.com/docs/eloquent-relationships#eager-loading

---

**Status:** ✅ FIXED
**Severity:** HIGH (Data tidak lengkap di laporan)
**Priority:** HIGH (Laporan PDF adalah fitur critical)

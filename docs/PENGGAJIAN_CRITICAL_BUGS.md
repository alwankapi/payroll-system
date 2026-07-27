# 🔴 CRITICAL BUGS FOUND - Modul Penggajian

## Executive Summary

Ditemukan **3 bug kritis** pada modul penggajian yang menyebabkan:
- ❌ Form Create/Edit **TIDAK BISA DIGUNAKAN** sama sekali
- ❌ Data potongan **TIDAK TERSIMPAN** ke database
- ❌ Perhitungan gaji **TIDAK BERJALAN** dengan benar

**Status:** 🔴 CRITICAL - **Sistem penggajian tidak berfungsi!**

---

## Bug #1: Form Tidak Mengirim Data yang Dibutuhkan ⚠️⚠️⚠️

### Severity: CRITICAL

### Deskripsi
Form create dan edit penggajian **tidak mengirim field required** yang divalidasi oleh FormRequest.

### File Terdampak
1. `resources/views/penggajian/create.blade.php`
2. `resources/views/penggajian/edit.blade.php`

### Root Cause Analysis

#### 1. Missing Fields di Form
Form HTML **TIDAK** mengirim:
- `total_potongan` (REQUIRED di validation!)
- `gaji_bersih` (REQUIRED di validation!)

Tapi FormRequest **REQUIRE** fields ini:
```php
// StorePenggajianRequest.php line 53-64
'total_potongan' => ['required', 'numeric', 'min:0'],
'gaji_bersih' => ['required', 'numeric', 'min:0'],
```

#### 2. Format Data Potongan Salah
Form mengirim:
```php
potongan_ids[] = [1, 2, 3]  // Hanya array of IDs
```

Service expect:
```php
// PenggajianService.php line 65
potongan_details => [
    ['potongan_id' => 1, 'nama_potongan' => '...', 'nilai_potongan' => 100000],
    ['potongan_id' => 2, 'nama_potongan' => '...', 'nilai_potongan' => 50000],
]
```

**Mismatch format = data potongan tidak tersimpan!**

#### 3. Controller Tidak Transform Data
Controller langsung pass `$request->validated()` ke Service tanpa transform `potongan_ids` menjadi `potongan_details`.

### Impact
- ❌ **100% validation gagal** - form tidak bisa submit
- ❌ **Potongan tidak tersimpan** - bahkan jika lolos validation
- ❌ **Gaji salah** - total_potongan dan gaji_bersih = 0 atau error
- ❌ **User tidak bisa create/update penggajian**

### Flow Error
```
User submit form
  ↓
Missing total_potongan & gaji_bersih
  ↓
Validation FAIL (400 Bad Request)
  ↓
Form dikembalikan dengan error
  ↓
❌ STUCK - tidak bisa lanjut
```

---

## Bug #2: Format Periode Tidak Konsisten

### Severity: HIGH

### Deskripsi
Format periode dari form tidak match dengan validation rules.

### Root Cause

**Form HTML:**
```html
<input type="month" name="periode" value="2026-07">
<!-- Return format: Y-m (contoh: 2026-07) -->
```

**Validation Rule:**
```php
'periode' => [
    'required',
    'date',
    'date_format:Y-m-d',  // ❌ Expect Y-m-d (2026-07-01)
],
```

**Mismatch**: `2026-07` !== `2026-07-01`

### Impact
- ❌ Validation selalu gagal di field periode
- ❌ Error message: "periode harus berupa tanggal yang valid"
- ❌ User bingung karena sudah pilih bulan tapi tetap error

### Flow Error
```
User pilih bulan: Juli 2026
  ↓
Form kirim: "2026-07"
  ↓
Validation expect: "2026-07-01"
  ↓
Validation FAIL
  ↓
❌ Error: periode tidak valid
```

---

## Bug #3: Data Attribute Salah di Create Form

### Severity: MEDIUM

### Deskripsi
Auto-fill tunjangan tidak berfungsi karena nama attribute salah.

### Root Cause

**File:** `resources/views/penggajian/create.blade.php` line 30

```html
<!-- ❌ SALAH -->
data-tunjangan="{{ $karyawan->jabatan->tunjangan }}"

<!-- ✅ BENAR -->
data-tunjangan="{{ $karyawan->jabatan->tunjangan_jabatan }}"
```

Jabatan model menggunakan field `tunjangan_jabatan` bukan `tunjangan`.

### Impact
- ❌ Tunjangan tidak ter-fill otomatis
- ❌ User harus input manual (rawan salah)
- ⚠️ Jika user lupa isi, validation error
- ⚠️ Jika user salah input, data gaji salah

---

## Testing yang Membuktikan Bug

### Test Case 1: Create Penggajian Baru
**Steps:**
1. Login sebagai admin
2. Buka `/penggajian/create`
3. Pilih karyawan
4. Pilih periode
5. Centang beberapa potongan
6. Submit form

**Expected:** Data tersimpan, redirect ke index
**Actual:** ❌ **Validation error: total_potongan wajib diisi**

### Test Case 2: Edit Penggajian
**Steps:**
1. Login sebagai admin
2. Buka penggajian dengan status draft
3. Klik edit
4. Ubah data apapun
5. Submit form

**Expected:** Data terupdate, redirect ke index
**Actual:** ❌ **Validation error: total_potongan wajib diisi**

### Test Case 3: Auto-fill dari Karyawan
**Steps:**
1. Buka create form
2. Pilih karyawan
3. Observe field gaji_pokok dan tunjangan

**Expected:** Gaji pokok dan tunjangan ter-fill otomatis
**Actual:** 
- ✅ Gaji pokok ter-fill (WORKS)
- ❌ Tunjangan kosong (BUG!)

---

## Kenapa Bug Ini Tidak Terdeteksi?

### 1. Tidak Ada Frontend Calculation
Form **tidak** menghitung `total_potongan` dan `gaji_bersih` di JavaScript before submit.

### 2. Tidak Ada Integration Test
Tidak ada test yang simulasi user submit form dengan real HTTP request.

### 3. Service Assume Data Sudah Benar
Service langsung terima data tanpa validation/transform di controller level.

### 4. Development Mode Mungkin Skip Validation
Mungkin developer test dengan:
- Manual database insert
- Seeder (yang tidak lewat form)
- API client (yang pass data lengkap)

Sehingga bug di form HTML tidak terdeteksi.

---

## Prioritas Fix

| Bug | Severity | Priority | Est. Time |
|-----|----------|----------|-----------|
| Bug #1 (Missing Fields) | CRITICAL | P0 | 2-3 hours |
| Bug #2 (Format Periode) | HIGH | P0 | 30 minutes |
| Bug #3 (Data Attribute) | MEDIUM | P1 | 5 minutes |

**Total Estimate:** 3-4 hours untuk fix semua bug

---

## Recommended Solutions

### Solution #1: Add JavaScript Calculation
Tambahkan JavaScript di form untuk:
1. Calculate `total_potongan` realtime saat checkbox potongan di-check/uncheck
2. Calculate `gaji_bersih` realtime: gaji_pokok + tunjangan - total_potongan
3. Update hidden input `total_potongan` dan `gaji_bersih` before submit
4. Display preview perhitungan ke user

### Solution #2: Fix Periode Format
Option A (Recommended): Ubah validation accept `Y-m` format
```php
'periode' => ['required', 'date', 'date_format:Y-m'],
```

Option B: Convert di JavaScript sebelum submit
```js
// Ubah "2026-07" menjadi "2026-07-01"
periode.value = periode.value + '-01';
```

### Solution #3: Fix Data Attribute
```html
data-tunjangan="{{ $karyawan->jabatan->tunjangan_jabatan }}"
```

### Solution #4 (Long Term): Refactor Controller
Tambahkan method untuk transform `potongan_ids` menjadi `potongan_details`:
```php
protected function preparePotonganData($karyawan, $potonganIds) {
    // Calculate potongan details dari IDs
    // Return proper format untuk Service
}
```

---

## Next Steps

1. ✅ Document semua bugs (DONE - dokumen ini)
2. ⏳ Implement solutions
3. ⏳ Write integration tests
4. ⏳ Manual testing semua flow
5. ⏳ Deploy to production

---

## Contact

Untuk pertanyaan atau diskusi terkait bugs ini:
- Prioritaskan Bug #1 (CRITICAL)
- Review code: `resources/views/penggajian/*.blade.php`
- Review logic: `app/Http/Controllers/PenggajianController.php`
- Review service: `app/Services/PenggajianService.php`

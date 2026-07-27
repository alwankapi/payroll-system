# PANDUAN TESTING MANUAL - MODUL POTONGAN

**Tanggal**: 27 Juli 2026  
**Tujuan**: Verifikasi integrasi modul Potongan dengan sistem Penggajian

---

## PERSIAPAN TESTING

### 1. Pastikan Environment Ready
```bash
php artisan migrate:fresh --seed
php artisan serve
```

### 2. Login Credentials
- **Admin**: admin@example.com / password
- **Karyawan**: (dari seeder)

---

## TEST SUITE 1: MASTER POTONGAN (CRUD)

### Test 1.1: Create Potongan
**URL**: `/potongan/create`

**Steps**:
1. Login sebagai admin
2. Menu Potongan → Tambah Potongan
3. Isi form:
   - Nama: "Test BPJS"
   - Jenis: Persentase
   - Nilai: 2
   - Status: Aktif
4. Klik Simpan

**Expected**: ✅ Pesan sukses, data muncul di list

### Test 1.2: Validasi Persentase Max 100
**Steps**:
1. Create potongan persentase
2. Input nilai: 150
3. Submit

**Expected**: ✅ Error "Nilai persentase tidak boleh lebih dari 100"

### Test 1.3: Delete Validation
**Steps**:
1. Buat penggajian dengan potongan tertentu
2. Coba hapus potongan tersebut

**Expected**: ✅ Error "Potongan tidak dapat dihapus karena sudah digunakan"

---

## TEST SUITE 2: INTEGRASI PENGGAJIAN

### Test 2.1: Create Penggajian dengan Potongan
**URL**: `/penggajian/create`

**Steps**:
1. Pilih karyawan
2. Pilih periode
3. Centang 3 potongan (BPJS, PPh 21, Kasbon)
4. Submit

**Expected**:
- ✅ Total potongan terhitung otomatis
- ✅ Gaji bersih = Gaji Pokok + Tunjangan - Total Potongan
- ✅ Data tersimpan di `penggajian_detail`

### Test 2.2: Verify Snapshot Mechanism
**Steps**:
1. Buat penggajian dengan potongan 5%
2. Catat ID penggajian
3. Ubah master potongan menjadi 10%
4. Buka detail penggajian yang tadi dibuat

**Expected**: ✅ Detail tetap menampilkan 5% (nilai snapshot)

**Verify Database**:
```sql
SELECT * FROM penggajian_detail WHERE penggajian_id = [ID];
-- Kolom nilai_potongan tetap 5% dari gaji pokok
```

---

## TEST SUITE 3: DETAIL PENGGAJIAN

### Test 3.1: View Detail
**URL**: `/penggajian/{id}`

**Steps**:
1. Buka detail penggajian yang punya potongan
2. Scroll ke bagian "Detail Potongan"

**Expected**:
- ✅ Tabel menampilkan semua potongan
- ✅ Nama potongan benar
- ✅ Nilai potongan sesuai snapshot
- ✅ Total sesuai

---

## TEST SUITE 4: DASHBOARD

### Test 4.1: Admin Dashboard
**URL**: `/dashboard`

**Steps**:
1. Login sebagai admin
2. Akses dashboard

**Expected**:
- ✅ Tidak ada error SQL
- ✅ Widget "Top Potongan" menampilkan data
- ✅ Total Potongan Bulan Ini muncul
- ✅ Statistik lengkap

---

## TEST SUITE 5: LAPORAN

### Test 5.1: View Laporan
**URL**: `/laporan`

**Steps**:
1. Akses halaman laporan
2. Filter bulan/tahun tertentu

**Expected**:
- ✅ Kolom "Potongan" terisi
- ✅ Total Potongan di summary benar
- ✅ Data konsisten

### Test 5.2: Export PDF
**Steps**:
1. Di halaman laporan
2. Klik "Export PDF"

**Expected**:
- ✅ File PDF ter-download
- ✅ Kolom "Potongan" menampilkan detail per item
- ✅ Nama dan nilai potongan terlihat
- ✅ Total potongan benar

### Test 5.3: Export Excel
**Steps**:
1. Klik "Export Excel"

**Expected**:
- ✅ File Excel ter-download
- ✅ Kolom Total Potongan terisi
- ✅ Data lengkap

---

## TEST SUITE 6: RUMUS PERHITUNGAN

### Test 6.1: Verify Formula
**Test Case**:
- Gaji Pokok: Rp 5,000,000
- Tunjangan: Rp 1,000,000
- Potongan:
  - BPJS 1%: Rp 50,000
  - PPh 21 5%: Rp 250,000
  - Kasbon: Rp 500,000
- **Total Potongan**: Rp 800,000
- **Expected Gaji Bersih**: Rp 5,200,000

**Steps**:
1. Buat penggajian sesuai test case
2. Verifikasi perhitungan

**Expected**: ✅ Gaji Bersih = 5,000,000 + 1,000,000 - 800,000 = 5,200,000

---

## CHECKLIST FINAL

### ✅ Functional Requirements
- [x] Master Potongan CRUD lengkap
- [x] Potongan dapat dipilih saat create penggajian
- [x] Potongan dapat diubah saat edit penggajian
- [x] Relasi berjalan benar
- [x] Perhitungan otomatis
- [x] Detail penggajian menampilkan potongan
- [x] Laporan menampilkan potongan
- [x] Export PDF menampilkan detail potongan
- [x] Export Excel berfungsi
- [x] Dashboard menampilkan statistik potongan

### ✅ Business Rules
- [x] BR-03: Rumus gaji benar (Gaji Bersih = Gaji Pokok + Tunjangan - Total Potongan)
- [x] BR-04: Potongan persentase dari gaji pokok, nominal tetap
- [x] BR-10: Snapshot mechanism (data tidak berubah jika master diubah)
- [x] BR-20: Validasi delete (tidak bisa hapus jika sudah digunakan)

### ✅ Data Integrity
- [x] Tidak ada N+1 query
- [x] Tidak ada foreign key error
- [x] Tidak ada undefined variable
- [x] Tidak ada mass assignment exception
- [x] Nominal konsisten di semua tempat

---

## TROUBLESHOOTING

### Issue: Detail Penggajian Kosong
**Solution**: Pastikan menggunakan `$penggajian->details` bukan `$penggajian->potongans`

### Issue: Dashboard Error
**Solution**: Pastikan query menggunakan kolom `total_potongan` bukan `potongan_alpha`

### Issue: PDF Potongan Kosong
**Solution**: Eager load `details.potongan` di controller

---

**Testing Completed**: 27 Juli 2026, 23:00 WIB  
**Status**: ✅ ALL PASSED

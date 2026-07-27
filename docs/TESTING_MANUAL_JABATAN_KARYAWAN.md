# Panduan Testing Manual - Modul Jabatan & Karyawan

**Tanggal**: 27 Juli 2026  
**Target**: Memastikan semua operasi CRUD berjalan tanpa error

---

## PERSIAPAN TESTING

### 1. Setup Database
```bash
php artisan migrate:fresh --seed
```

### 2. Jalankan Server
```bash
php artisan serve
```

### 3. Login sebagai Admin
- URL: `http://localhost:8000/login`
- Email: `admin@example.com`
- Password: `password`

---

## A. TESTING MODUL JABATAN

### Test 1: CREATE Jabatan

**Langkah:**
1. Buka menu **Master Data → Jabatan**
2. Klik tombol **"Tambah Jabatan"**
3. Isi form:
   - Nama Jabatan: `Manager IT`
   - Gaji Pokok: `8000000`
   - Tunjangan Jabatan: `2000000`
4. Klik **"Simpan Jabatan"**

**Expected:**
- ✅ Form tersubmit tanpa error
- ✅ Redirect ke halaman index jabatan
- ✅ Muncul pesan sukses "Jabatan berhasil ditambahkan"
- ✅ Data muncul di tabel dengan Total Gaji = Rp 10.000.000

**Verifikasi:**
- [ ] Form tidak error
- [ ] Data tersimpan di database
- [ ] Ringkasan gaji (JavaScript) berfungsi
- [ ] Total gaji dihitung otomatis

---

### Test 2: READ Jabatan (List & Detail)

**Langkah:**
1. Di halaman index jabatan, pastikan data tampil
2. Test fitur **Search**: ketik `Manager`
3. Test fitur **Sort**: klik header kolom "Gaji Pokok"
4. Klik tombol **"Lihat Detail"** (ikon mata) pada salah satu jabatan

**Expected:**
- ✅ Tabel menampilkan data dengan benar
- ✅ Search berfungsi, hanya data yang match yang muncul
- ✅ Sort ascending/descending berfungsi
- ✅ Halaman detail menampilkan semua informasi jabatan
- ✅ Total gaji terhitung dengan benar

**Verifikasi:**
- [ ] List tampil tanpa error
- [ ] Search berfungsi
- [ ] Sort berfungsi
- [ ] Detail lengkap tampil
- [ ] Pagination berfungsi (jika data > 10)

---

### Test 3: UPDATE Jabatan

**Langkah:**
1. Di halaman index jabatan, klik tombol **"Edit"** (ikon pensil)
2. Ubah data:
   - Tunjangan Jabatan: `2500000`
3. Klik **"Update Jabatan"**

**Expected:**
- ✅ Form terisi dengan data lama
- ✅ Form tersubmit tanpa error
- ✅ Redirect ke halaman index
- ✅ Muncul pesan sukses "Jabatan berhasil diperbarui"
- ✅ Data ter-update di tabel
- ✅ Total gaji berubah menjadi Rp 10.500.000

**Verifikasi:**
- [ ] Form tidak error
- [ ] Old value ter-load dengan benar
- [ ] Update berhasil
- [ ] Total gaji recalculate dengan benar

---

### Test 4: DELETE Jabatan

**Langkah:**
1. Buat jabatan baru untuk dihapus (tanpa karyawan)
2. Di halaman index, klik tombol **"Hapus"** (ikon tempat sampah)
3. Konfirmasi hapus

**Expected:**
- ✅ Muncul konfirmasi popup
- ✅ Data terhapus setelah konfirmasi
- ✅ Muncul pesan sukses "Jabatan berhasil dihapus"
- ✅ Data hilang dari tabel

**Test Delete dengan Constraint:**
1. Coba hapus jabatan yang sudah digunakan karyawan
2. Expected: Muncul error/pesan bahwa jabatan masih digunakan

**Verifikasi:**
- [ ] Delete berfungsi untuk jabatan kosong
- [ ] Foreign key constraint mencegah delete jabatan yang digunakan

---

## B. TESTING MODUL KARYAWAN

### Test 5: CREATE Karyawan

**Langkah:**
1. Buka menu **Master Data → Karyawan**
2. Klik tombol **"Tambah Karyawan"**
3. Isi form:
   - NIK: `3201012101900001`
   - Nama Lengkap: `Ahmad Fadillah`
   - Email: `ahmad.fadillah@test.com`
   - No. Telepon: `081234567890`
   - Jabatan: Pilih `Manager IT`
   - Password: `password123` (atau kosongkan)
   - Tanggal Masuk: `2026-01-01`
   - No. Rekening: `1234567890`
   - Status: `Aktif`
   - Alamat: `Jl. Test No. 123`
4. Klik **"Simpan Karyawan"**

**Expected:**
- ✅ Form tersubmit tanpa error
- ✅ User account otomatis dibuat
- ✅ Redirect ke halaman index karyawan
- ✅ Muncul pesan sukses "Karyawan berhasil ditambahkan"
- ✅ Data muncul di tabel

**Verifikasi:**
- [ ] Form tidak error
- [ ] Data karyawan tersimpan
- [ ] User account ter-create
- [ ] Email dapat login

---

### Test 6: READ Karyawan (List & Detail)

**Langkah:**
1. Di halaman index karyawan, pastikan data tampil
2. Test **Search**: ketik nama atau NIK
3. Test **Filter Jabatan**: pilih jabatan tertentu
4. Test **Filter Status**: pilih Aktif/Nonaktif
5. Klik tombol **"Lihat Detail"** pada salah satu karyawan

**Expected:**
- ✅ Tabel menampilkan: NIK, Nama, Jabatan, Email (dari user), Status
- ✅ Email tampil dari relasi `user` tanpa error
- ✅ Search berfungsi
- ✅ Filter jabatan berfungsi
- ✅ Filter status berfungsi
- ✅ Halaman detail menampilkan semua informasi lengkap

**Verifikasi:**
- [ ] List tampil tanpa error `$karyawan->user->email`
- [ ] Search berfungsi
- [ ] Filter berfungsi
- [ ] Detail lengkap tampil
- [ ] Pagination berfungsi

---

### Test 7: UPDATE Karyawan

**Langkah:**
1. Di halaman index karyawan, klik tombol **"Edit"**
2. Periksa field yang ada:
   - ✅ NIK (editable)
   - ✅ Nama Lengkap (editable)
   - ✅ No. Telepon (editable)
   - ✅ No. Rekening (editable)
   - ✅ Jabatan (editable)
   - ✅ Tanggal Masuk (editable)
   - ✅ Status (editable)
   - ✅ Alamat (editable)
   - ❌ Email (TIDAK ADA - correct!)
3. Ubah beberapa data, klik **"Update Karyawan"**

**Expected:**
- ✅ Form tidak ada field `email`
- ✅ Field `tanggal_masuk` ada (bukan `tanggal_bergabung`)
- ✅ Field `no_rekening` ada
- ✅ Form tersubmit tanpa error
- ✅ Data ter-update
- ✅ Muncul pesan sukses

**Verifikasi:**
- [ ] Tidak ada field email di edit form
- [ ] Field tanggal_masuk ada dan berfungsi
- [ ] Field no_rekening ada
- [ ] Update berhasil tanpa error

---

### Test 8: DELETE Karyawan

**Langkah:**
1. Buat karyawan baru untuk dihapus (tanpa riwayat gaji)
2. Di halaman index, klik tombol **"Hapus"**
3. Konfirmasi hapus

**Expected:**
- ✅ Muncul konfirmasi popup
- ✅ Data terhapus setelah konfirmasi
- ✅ User account juga terhapus (cascade)
- ✅ Muncul pesan sukses

**Test Delete dengan Constraint:**
1. Coba hapus karyawan yang memiliki riwayat penggajian
2. Expected: Error/pesan bahwa karyawan tidak bisa dihapus

**Verifikasi:**
- [ ] Delete berfungsi untuk karyawan baru
- [ ] Foreign key constraint mencegah delete karyawan dengan riwayat gaji

---

## C. TESTING RELASI & INTEGRASI

### Test 9: Relasi Jabatan-Karyawan

**Langkah:**
1. Buat 1 Jabatan baru
2. Buat 2 Karyawan dengan jabatan tersebut
3. Lihat detail jabatan, cek jumlah karyawan
4. Coba hapus jabatan

**Expected:**
- ✅ Detail jabatan menampilkan jumlah karyawan
- ✅ Tidak bisa hapus jabatan yang sudah digunakan
- ✅ Pesan warning muncul

**Verifikasi:**
- [ ] Relasi hasMany berfungsi
- [ ] Foreign key constraint berfungsi
- [ ] Warning message tampil

---

### Test 10: Validation Testing

**Test untuk Jabatan:**
1. Submit form kosong → Expected: error required
2. Nama jabatan duplikat → Expected: error unique
3. Gaji negatif → Expected: error min:0

**Test untuk Karyawan:**
1. Submit form kosong → Expected: error required
2. NIK duplikat → Expected: error unique
3. Email duplikat → Expected: error unique
4. Format email salah → Expected: error email

**Verifikasi:**
- [ ] Semua validation rule berfungsi
- [ ] Error message tampil dengan jelas
- [ ] Old input preserved

---

## D. TESTING PERFORMA

### Test 11: N+1 Query Check

**Cara Manual:**
1. Buka `config/app.php`, set `debug` = `true`
2. Install Laravel Debugbar (opsional)
3. Buka halaman index Jabatan
4. Buka halaman index Karyawan
5. Periksa query count di Laravel Debugbar

**Expected:**
- ✅ Index Jabatan: Maksimal 3-4 queries
- ✅ Index Karyawan: Maksimal 4-5 queries (dengan eager loading `jabatan` dan `user`)
- ✅ Tidak ada N+1 query problem

**Verifikasi:**
- [ ] Eager loading digunakan di KaryawanController
- [ ] Query count minimal

---

## CHECKLIST AKHIR

### ✅ Jabatan Module
- [ ] Create berfungsi tanpa error
- [ ] Read (list & detail) berfungsi
- [ ] Update berfungsi tanpa error
- [ ] Delete berfungsi dengan constraint
- [ ] Search & sort berfungsi
- [ ] Validation berfungsi
- [ ] Total gaji terhitung otomatis

### ✅ Karyawan Module
- [ ] Create berfungsi tanpa error
- [ ] Read (list & detail) berfungsi
- [ ] Update berfungsi tanpa error (tanpa field email)
- [ ] Delete berfungsi dengan constraint
- [ ] Search & filter berfungsi
- [ ] Validation berfungsi
- [ ] Email dari relasi user tampil benar

### ✅ General
- [ ] Tidak ada error 500
- [ ] Tidak ada undefined variable
- [ ] Tidak ada broken route
- [ ] Flash message tampil
- [ ] Redirect berfungsi
- [ ] Pagination berfungsi

---

## NOTES

Jika menemukan error, catat:
1. URL dimana error terjadi
2. Langkah reproduksi
3. Screenshot error message
4. Log dari `storage/logs/laravel.log`

**Happy Testing! 🚀**

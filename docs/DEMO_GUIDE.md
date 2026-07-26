# 🎯 PANDUAN DEMO KLIEN - Sistem Penggajian

## 📋 PERSIAPAN SEBELUM DEMO

### 1. Pastikan Server Berjalan
```bash
php artisan serve
# Aplikasi akan berjalan di http://127.0.0.1:8000
```

### 2. Kredensial Login
**Admin:**
- Email: `admin@payroll.test`
- Password: `password`

**Karyawan (untuk test):**
- Email: `karyawan1@payroll.test` sampai `karyawan25@payroll.test`
- Password: `password`

---

## 🚀 SKENARIO DEMO (15-20 Menit)

### TAHAP 1: Dashboard & Overview (2 menit)
1. **Login sebagai Admin**
   - Buka http://127.0.0.1:8000
   - Login dengan kredensial admin
   
2. **Tampilkan Dashboard**
   - Jelaskan statistik: Total Karyawan (25), Total Jabatan (10), Total Penggajian (50)
   - Tunjukkan chart penggajian 6 bulan terakhir
   - Highlight recent activities

---

### TAHAP 2: Master Data - Kelola Karyawan (3 menit)

1. **Lihat Daftar Karyawan**
   - Klik menu "Karyawan"
   - Tunjukkan data realistis: Andi Saputra, Budi Santoso, dll
   - Filter by jabatan/status

2. **Tambah Karyawan Baru**
   - Klik "Tambah Karyawan"
   - Isi form:
     - Nama: "Rudi Hartono"
     - Email: "rudi@company.com"
     - Jabatan: Pilih "Staff IT"
     - Status: Aktif
   - Simpan → Sukses!

3. **Lihat Detail Karyawan**
   - Klik salah satu karyawan
   - Tampilkan info lengkap + riwayat penggajian

---

### TAHAP 3: Proses Penggajian (5 menit)

1. **Generate Penggajian Massal**
   - Klik menu "Penggajian" → "Generate Bulk"
   - Pilih periode: Agustus 2026
   - Klik "Generate"
   - Sistem otomatis hitung gaji untuk semua karyawan aktif

2. **Review Penggajian**
   - Lihat list penggajian yang baru dibuat (status: Draft)
   - Klik salah satu untuk melihat detail
   - **Tunjukkan perhitungan:**
     ```
     Gaji Pokok:     Rp 4.500.000
     Tunjangan:      Rp   700.000
     Total Potongan: Rp   135.000
     ----------------------------
     Gaji Bersih:    Rp 5.065.000 ✓
     ```

3. **Ubah Status (Workflow)**
   - Status: Draft → Diproses → Disetujui → Dibayar
   - Tunjukkan validasi workflow (tidak bisa loncat status)

---

### TAHAP 4: Cetak Slip Gaji (3 menit)

1. **Preview Slip Gaji**
   - Dari detail penggajian, klik "Preview Slip Gaji"
   - PDF terbuka di tab baru
   - Tampilan profesional dengan logo & detail lengkap

2. **Download Slip Gaji**
   - Klik "Download PDF"
   - File terdownload dengan nama: `slip-gaji-[nama]-[periode].pdf`

---

### TAHAP 5: Laporan (3 menit)

1. **Generate Laporan Penggajian**
   - Klik menu "Laporan"
   - Filter by periode (misal: Juli 2026)
   - Filter by status: "Dibayar"

2. **Export Laporan**
   - **Export Excel:** Data tabular lengkap untuk analisis
   - **Export PDF:** Laporan formal untuk arsip

---

### TAHAP 6: Akses Karyawan (2 menit)

1. **Logout dari Admin**

2. **Login sebagai Karyawan**
   - Email: `karyawan1@payroll.test`
   - Password: `password`

3. **Dashboard Karyawan**
   - Karyawan hanya lihat data diri sendiri
   - Riwayat penggajian pribadi
   - Download slip gaji sendiri

---

## 🎨 POIN-POIN HIGHLIGHT UNTUK KLIEN

### ✅ Keunggulan Sistem

1. **Otomatis & Akurat**
   - Generate penggajian massal 1 klik
   - Perhitungan otomatis 100% akurat
   - Formula: `Gaji Bersih = Gaji Pokok + Tunjangan - Potongan`

2. **Potongan Fleksibel**
   - Support nominal tetap (Rp 50.000)
   - Support persentase (5% dari gaji pokok)
   - Bisa aktif/nonaktif sewaktu-waktu

3. **Workflow Approval**
   - 6 status: Draft → Diproses → Disetujui → Dibayar
   - Status bisa ditolak/dibatalkan
   - Status Dibayar = final (tidak bisa diubah)

4. **Security & Access Control**
   - Role-based: Admin vs Karyawan
   - Karyawan hanya bisa akses data sendiri
   - Admin full access semua data

5. **Laporan Lengkap**
   - Export Excel untuk analisis
   - Export PDF untuk arsip formal
   - Filter by periode & status

---

## 💡 TIPS PRESENTASI

### DO:
- ✅ Fokus pada kemudahan penggunaan
- ✅ Tunjukkan hasil perhitungan real-time
- ✅ Demonstrasi workflow lengkap end-to-end
- ✅ Highlight keakuratan perhitungan
- ✅ Siapkan jawaban untuk pertanyaan umum

### DON'T:
- ❌ Jangan terlalu technical
- ❌ Jangan skip tahap penting
- ❌ Jangan terburu-buru
- ❌ Jangan lupa logout setelah demo admin

---

## 📞 PERTANYAAN UMUM KLIEN

**Q: Apakah bisa custom formula gaji?**
A: Ya, bisa disesuaikan per jabatan. Gaji pokok & tunjangan bisa diubah kapan saja.

**Q: Berapa lama generate penggajian untuk 100 karyawan?**
A: Sekitar 5-10 detik. Sistem sangat cepat dan efisien.

**Q: Apakah data lama berubah kalau master data diubah?**
A: Tidak! Sistem menggunakan snapshot. Penggajian yang sudah dibuat tidak akan berubah walau master data diupdate.

**Q: Bisa export ke Excel?**
A: Ya, semua laporan bisa di-export ke Excel (.xlsx) dan PDF.

**Q: Apakah mobile-friendly?**
A: Ya, tampilan responsive dan bisa diakses dari mobile/tablet.

---

## ✅ CHECKLIST SEBELUM DEMO

- [ ] Server Laravel sudah running (php artisan serve)
- [ ] Database terisi data sample (26 users, 25 karyawan, 50 penggajian)
- [ ] Browser sudah dibuka di http://127.0.0.1:8000
- [ ] Kredensial login sudah dicatat
- [ ] Internet connection stabil (untuk assets)
- [ ] Screen sharing/projector sudah ready
- [ ] Backup plan jika ada error

---

**Good luck with the demo! 🚀**

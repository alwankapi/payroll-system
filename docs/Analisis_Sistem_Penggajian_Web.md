# Dokumen Analisis Sistem
## Sistem Informasi Penggajian Berbasis Web

| | |
|---|---|
| **Nama Proyek** | Sistem Penggajian (Payroll Web System) |
| **Versi Dokumen** | 1.0 |
| **Tanggal** | 23 Juli 2026 |
| **Disusun oleh** | Senior System Analyst |
| **Referensi** | PRD Sistem Penggajian Berbasis Web v1.0 |

Dokumen ini adalah hasil analisis sistem yang disusun berdasarkan PRD, mencakup identifikasi masalah, solusi, pihak terkait, kebutuhan, alur bisnis, dan aturan bisnis dari Sistem Penggajian.

---

## Daftar Isi
1. Identifikasi Masalah
2. Solusi
3. Stakeholder
4. Aktor
5. Kebutuhan Fungsional
6. Kebutuhan Non Fungsional
7. Flow Bisnis Sistem
8. Business Rules

---

## 1. Identifikasi Masalah

Analisis memakai kerangka **PIECES** terhadap proses penggajian manual/semi-manual (spreadsheet) yang berjalan saat ini.

| Kategori | Masalah |
|---|---|
| Performance | Perhitungan gaji manual tiap periode makan waktu lama; rawan terlambat bayar. |
| Information | Data jabatan/karyawan/potongan tersebar di banyak file, tidak konsisten, sulit ditelusuri riwayatnya. |
| Economy | Jam kerja staf habis untuk hitung manual; salah hitung berisiko kerugian finansial. |
| Control | Tidak ada pembatasan akses baku — siapa saja bisa ubah data gaji tanpa jejak/log. |
| Efficiency | Entri data berulang (redundan) di banyak spreadsheet berbeda untuk kebutuhan yang sama. |
| Service | Karyawan sulit dapat slip gaji tepat waktu; tidak ada akses mandiri (self-service) ke riwayat gaji sendiri. |

## 2. Solusi

| Masalah (PIECES) | Solusi / Fitur Terkait |
|---|---|
| Performance | Perhitungan gaji otomatis + fitur generate massal per periode. |
| Information | Basis data terpusat: master Jabatan, Karyawan, Potongan dalam satu sistem. |
| Economy | Otomasi hitung gaji mengurangi jam kerja manual & risiko salah hitung. |
| Control | Role Admin/Karyawan + middleware pembatas akses per modul. |
| Efficiency | Satu sumber data (single source of truth), tidak ada entri berulang. |
| Service | Slip gaji PDF otomatis + dashboard Karyawan untuk akses mandiri riwayat gaji. |

## 3. Stakeholder

| Stakeholder | Kepentingan |
|---|---|
| Pemilik/Manajemen | Butuh laporan penggajian akurat untuk pengambilan keputusan & anggaran. |
| Bagian HRD/Kepegawaian | Mengelola data jabatan & karyawan (berperan sebagai Admin di sistem). |
| Bagian Keuangan | Memakai laporan penggajian untuk pencatatan & realisasi pembayaran. |
| Karyawan | Penerima gaji; butuh slip gaji & transparansi rincian perhitungan. |
| Tim Pengembang | Membangun, menguji, dan memelihara sistem. |
| Dosen/Asesor | Menilai kesesuaian sistem dengan kompetensi analis sistem. |

## 4. Aktor

| Aktor | Deskripsi | Interaksi Utama |
|---|---|---|
| Admin | Pengelola sistem (HRD/staf penggajian) dengan akses penuh | Login, CRUD Jabatan/Karyawan/Potongan/Penggajian, generate & finalisasi gaji, cetak slip, lihat laporan |
| Karyawan | Pengguna dengan akses terbatas ke data pribadi | Login, lihat dashboard pribadi, unduh slip gaji sendiri, lihat riwayat gaji sendiri |

## 5. Kebutuhan Fungsional

**5.1 Admin**
1. Login ke sistem.
2. Mengelola (tambah/lihat/ubah/hapus) data Jabatan.
3. Mengelola data Karyawan, termasuk menonaktifkan karyawan yang berhenti.
4. Mengelola data jenis Potongan.
5. Generate data Penggajian per karyawan atau massal per periode.
6. Meninjau & mengubah status Penggajian (Draft → Final → Dibayar).
7. Mencetak/mengunduh slip gaji karyawan.
8. Melihat & mengekspor laporan penggajian.
9. Melihat ringkasan data di dashboard Admin.

**5.2 Karyawan**
1. Login ke sistem.
2. Melihat dashboard pribadi (ringkasan gaji periode terakhir).
3. Melihat & mengunduh slip gaji milik sendiri.
4. Melihat riwayat penggajian milik sendiri.
5. Logout.

**5.3 Sistem (Otomatis)**
1. Menghitung gaji bersih otomatis: gaji pokok + tunjangan − total potongan.
2. Mencegah duplikasi penggajian untuk karyawan + periode yang sama.
3. Menyimpan snapshot gaji pokok/tunjangan/potongan tiap penggajian dibuat.
4. Membatasi akses halaman/menu sesuai role (middleware).
5. Mengunci data Final/Dibayar dari perubahan langsung.

## 6. Kebutuhan Non Fungsional

- **Security** — password ter-hash (bcrypt) via Breeze, CSRF protection, akses data gaji dibatasi per role.
- **Performance** — respon halaman CRUD ≤ 2 detik; generate massal < 1 menit untuk 500 karyawan.
- **Usability** — UI Tailwind CSS responsif, berbahasa Indonesia, format Rupiah & tanggal lokal.
- **Reliability** — validasi input di server (Form Request) mencegah data tidak valid tersimpan.
- **Maintainability** — struktur MVC Laravel, PSR standard, kode terdokumentasi.
- **Testability** — fungsi inti (perhitungan gaji, CRUD) disertai unit test & integration test.
- **Scalability** — struktur database tahan terhadap pertumbuhan data karyawan/histori gaji.
- **Compatibility** — berjalan baik di browser modern (Chrome, Firefox, Edge, Safari).
- **Data Integrity** — foreign key constraint antar tabel Jabatan-Karyawan-Potongan-Penggajian.
- **Availability** — target uptime minimal 99% di lingkungan produksi/demo.

## 7. Flow Bisnis Sistem

*Sebelum sistem:* data jabatan/karyawan/potongan tersebar di spreadsheet, gaji dihitung manual tiap periode oleh staf.

*Alur proses penggajian (per periode), sesudah sistem berjalan:*

1. **[Admin]** Pastikan data Jabatan & Karyawan aktif sudah lengkap dan valid.
2. **[Admin]** Pastikan jenis Potongan aktif sudah sesuai ketentuan periode berjalan.
3. **[Admin]** Jalankan "Generate Penggajian" untuk seluruh karyawan aktif pada periode berjalan.
4. **[Sistem]** Ambil gaji pokok & tunjangan dari data Jabatan tiap karyawan.
5. **[Sistem]** Hitung total potongan berdasarkan jenis potongan aktif yang berlaku.
6. **[Sistem]** Hitung gaji bersih (gaji pokok + tunjangan − total potongan), simpan sebagai Penggajian berstatus Draft.
7. **[Admin]** Tinjau/verifikasi kewajaran data penggajian berstatus Draft.
8. **[Admin]** Ubah status jadi Final setelah verifikasi selesai; data terkunci dari perubahan langsung.
9. **[Admin]** Ubah status jadi Dibayar setelah proses transfer gaji selesai (di luar sistem).
10. **[Sistem]** Aktifkan opsi unduh Slip Gaji PDF untuk penggajian berstatus Final/Dibayar.
11. **[Karyawan]** Login, buka dashboard, unduh slip gaji miliknya sendiri.
12. **[Admin]** Buka menu Laporan Penggajian untuk rekap & pelaporan ke manajemen di akhir periode.

## 8. Business Rules

| ID | Aturan |
|---|---|
| BR-01 | Hanya Admin yang dapat create/update/delete data Jabatan, Karyawan, dan Potongan. |
| BR-02 | Karyawan hanya punya akses baca ke data miliknya sendiri. |
| BR-03 | Gaji Bersih = Gaji Pokok + Tunjangan Jabatan − Total Potongan. |
| BR-04 | Potongan "Persentase" dihitung dari Gaji Pokok; "Nominal Tetap" dihitung sebagai nilai tetap. |
| BR-05 | Satu karyawan hanya satu data penggajian per periode (bulan-tahun) yang sama. |
| BR-06 | Penggajian Final/Dibayar tidak dapat dihapus, hanya diubah lewat reopen oleh Admin. |
| BR-07 | Slip gaji hanya bisa digenerate untuk penggajian berstatus Final/Dibayar. |
| BR-08 | Karyawan Nonaktif tidak diikutkan pada generate penggajian periode berikutnya. |
| BR-09 | Jabatan yang masih dipakai karyawan aktif tidak dapat dihapus. |
| BR-10 | Perubahan data master Jabatan/Potongan tidak memengaruhi penggajian yang sudah dibuat (snapshot/historis). |
| BR-11 | Akun login hanya dibuat Admin, terhubung ke satu data Karyawan. |

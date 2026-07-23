# Perancangan UI/UX
## Sistem Informasi Penggajian Berbasis Web

| | |
|---|---|
| **Nama Proyek** | Sistem Penggajian (Payroll Web System) |
| **Versi Dokumen** | 1.0 |
| **Tanggal** | 23 Juli 2026 |
| **Disusun oleh** | Senior UI/UX Designer |
| **Referensi** | PRD v1.0, Analisis Sistem v1.0, Diagram UML v1.0 |
| **Tech** | Laravel 12 (Blade) + Tailwind CSS |

---

## Daftar Isi
1. Prinsip & Sistem Desain
2. Halaman 1 — Login
3. Halaman 2 — Dashboard
4. Halaman 3 — Data Karyawan
5. Halaman 4 — Data Jabatan
6. Halaman 5 — Data Potongan
7. Halaman 6 — Data Penggajian
8. Halaman 7 — Detail Slip Gaji
9. Halaman 8 — Laporan
10. Halaman 9 — Profil Admin
11. Sitemap Aplikasi
12. Daftar Komponen UI

---

## 1. Prinsip & Sistem Desain

| Tema | Penerapan |
|---|---|
| Modern | Flat design, soft shadow (shadow-sm/md), rounded-xl pada card & tombol |
| Minimalis | Whitespace luas, hierarki visual jelas, hindari elemen dekoratif berlebih |
| Profesional | Palet warna netral dgn aksen terbatas, tipografi konsisten |
| Responsive | Mobile-first, breakpoint Tailwind default (sm/md/lg/xl) |

**Palet Warna**

| Peran | Tailwind | Penggunaan |
|---|---|---|
| Primary | indigo-600 / indigo-700 | Tombol utama, aksen navigasi aktif, brand |
| Netral | slate-50 (bg), slate-800 (teks) | Latar halaman, teks utama |
| Sukses | emerald-100 / emerald-700 | Status Aktif, Dibayar |
| Peringatan | amber-100 / amber-700 | Status Draft/Pending |
| Info | blue-100 / blue-700 | Status Final |
| Bahaya | rose-100 / rose-600 | Nonaktif, hapus, error |

**Tipografi & Ikon**
- Font: sans-serif default Tailwind (Inter/system-ui). Heading `text-xl`/`text-2xl font-semibold`, body `text-sm`/`text-base`, tabel `text-sm`.
- Ikon: Heroicons — outline untuk navigasi/aksi umum, solid untuk badge status.

**Responsive**
- Sidebar menjadi hamburger/off-canvas di bawah `md` (768px).
- Tabel lebar jadi scroll horizontal atau card-stack di bawah `sm` (640px).
- Grid statistik dashboard: 1 kolom (mobile) → 2 kolom (md) → 4 kolom (lg).

---

## 2. Halaman 1 — Login

| Aspek | Deskripsi |
|---|---|
| Tujuan Halaman | Autentikasi Admin/Karyawan sebelum masuk sistem |
| Layout | Split 2 kolom di desktop (kiri panel primary + branding, kanan form putih); stack 1 kolom di mobile |
| Komponen | Logo, input email, input password (toggle show/hide), checkbox "Ingat saya", tombol "Masuk", link "Lupa password?" |
| Warna | Panel kiri indigo-600, form kanan putih/slate-50, tombol submit indigo-600 solid |
| Ikon | envelope (email), lock-closed (password), eye/eye-slash (toggle) |
| Navigasi | Tanpa navbar; halaman standalone |
| UX Flow | Isi email & password → validasi → klik Masuk → loading state → redirect ke Dashboard sesuai role, atau tampil error di atas form |

## 3. Halaman 2 — Dashboard

| Aspek | Deskripsi |
|---|---|
| Tujuan Halaman | Ringkasan cepat kondisi sistem sesuai role |
| Layout | Sidebar kiri + topbar + grid card statistik + tabel/grafik ringkas (Admin); versi Karyawan lebih sederhana tanpa sidebar penuh |
| Komponen | Sidebar menu, topbar+dropdown avatar, card statistik (karyawan aktif, total gaji periode ini, jumlah jabatan), grafik tren gaji, tabel aktivitas terbaru |
| Warna | Background slate-50, card putih shadow-sm rounded-xl, aksen warna beda tiap card statistik |
| Ikon | users, briefcase, banknotes, chart-bar, bell |
| Navigasi | Sidebar tetap (collapsible di tablet, off-canvas di mobile) |
| UX Flow | Login berhasil → tampil ringkasan → klik card/menu → lanjut ke modul terkait |

## 4. Halaman 3 — Data Karyawan

| Aspek | Deskripsi |
|---|---|
| Tujuan Halaman | Kelola data karyawan (lihat, tambah, ubah, nonaktifkan) |
| Layout | Header + tombol "Tambah Karyawan", search bar & filter, tabel dgn pagination |
| Komponen | Search input, filter jabatan/status, tabel (NIK, nama, jabatan, status, aksi), badge status, modal form tambah/edit, pagination |
| Warna | Badge aktif emerald, nonaktif slate; tombol tambah indigo solid, tombol nonaktifkan rose |
| Ikon | magnifying-glass, funnel, pencil-square, user-minus, plus |
| Navigasi | Breadcrumb "Dashboard / Data Karyawan" |
| UX Flow | Cari/filter → Tambah → isi form (validasi inline) → simpan → notifikasi sukses → tabel refresh |

## 5. Halaman 4 — Data Jabatan

| Aspek | Deskripsi |
|---|---|
| Tujuan Halaman | Kelola data jabatan beserta gaji pokok & tunjangan |
| Layout | Header + tombol tambah + tabel ringkas |
| Komponen | Tabel (nama jabatan, gaji pokok, tunjangan, jumlah karyawan terkait, aksi), modal form, tombol hapus (disabled bila masih dipakai) |
| Warna | Skema utama; nominal gaji `font-semibold` slate-800 |
| Ikon | briefcase, banknotes, trash (pudar bila disabled) |
| Navigasi | Breadcrumb "Dashboard / Data Jabatan" |
| UX Flow | Tambah/Edit → isi nominal → simpan; hapus jabatan terpakai → ditolak dgn peringatan |

## 6. Halaman 5 — Data Potongan

| Aspek | Deskripsi |
|---|---|
| Tujuan Halaman | Kelola jenis potongan gaji (persentase/nominal) |
| Layout | Header + tombol tambah + tabel dgn toggle status langsung |
| Komponen | Tabel, toggle switch status_aktif, badge jenis potongan, modal form tambah/edit |
| Warna | Toggle aktif emerald, nonaktif slate; badge jenis dibedakan warna (persentase ungu, nominal biru) |
| Ikon | calculator, adjustments-horizontal |
| Navigasi | Breadcrumb "Dashboard / Data Potongan" |
| UX Flow | Toggle aktif/nonaktif langsung dari tabel, atau tambah/edit lewat modal → simpan |

## 7. Halaman 6 — Data Penggajian

| Aspek | Deskripsi |
|---|---|
| Tujuan Halaman | Generate & kelola status penggajian per periode |
| Layout | Filter periode (date-picker) + tombol besar "Generate Penggajian" + tabel hasil |
| Komponen | Date-picker periode, tombol Generate, tabel (nama, gaji pokok, potongan, gaji bersih, badge status), tombol "Lihat Detail", tombol ubah status |
| Warna | Badge status: amber (Draft), blue (Final), emerald (Dibayar); tombol Generate indigo mencolok |
| Ikon | calendar, bolt (generate), document-text, check-circle, banknotes |
| Navigasi | Breadcrumb "Dashboard / Data Penggajian"; klik baris → Detail Slip Gaji |
| UX Flow | Pilih periode → Generate → loading → tabel Draft muncul → tinjau → ubah Final → ubah Dibayar setelah transfer |

## 8. Halaman 7 — Detail Slip Gaji

| Aspek | Deskripsi |
|---|---|
| Tujuan Halaman | Tampilkan rincian gaji satu karyawan satu periode, siap unduh |
| Layout | Card tunggal terpusat menyerupai struktur slip gaji fisik |
| Komponen | Header identitas (nama, NIK, jabatan, periode), tabel pendapatan, tabel potongan, total gaji bersih ditonjolkan, tombol "Unduh PDF"/"Cetak" |
| Warna | Latar putih bersih, gaji bersih warna primary/emerald, garis pemisah slate-200 |
| Ikon | arrow-down-tray, printer, building-office |
| Navigasi | Breadcrumb "Dashboard / Data Penggajian / Detail Slip Gaji"; tombol kembali |
| UX Flow | Klik baris di Data Penggajian/riwayat → tampil detail → Unduh/Cetak → file PDF terunduh |

## 9. Halaman 8 — Laporan

| Aspek | Deskripsi |
|---|---|
| Tujuan Halaman | Rekap & ekspor laporan penggajian untuk manajemen |
| Layout | Panel filter (periode, jabatan, status) + card ringkasan angka + tabel/grafik rekap + tombol ekspor |
| Komponen | Filter form, card ringkasan, tabel rekap, grafik batang tren bulanan, tombol "Ekspor PDF"/"Ekspor Excel" |
| Warna | Grafik primary + aksen kedua; card ringkasan latar indigo-50 |
| Ikon | funnel, chart-bar, document-arrow-down |
| Navigasi | Breadcrumb "Dashboard / Laporan" |
| UX Flow | Pilih filter → Tampilkan → data & grafik muncul → Ekspor → pilih format → file terunduh |

## 10. Halaman 9 — Profil Admin

| Aspek | Deskripsi |
|---|---|
| Tujuan Halaman | Kelola data akun & keamanan milik Admin yang login |
| Layout | Card tunggal (foto profil + form info akun) + section terpisah "Ubah Password" |
| Komponen | Avatar/upload foto, input nama & email, form ubah password (lama, baru, konfirmasi), tombol simpan |
| Warna | Skema konsisten; tombol simpan indigo, area upload foto border dashed slate-300 |
| Ikon | user-circle, camera, key |
| Navigasi | Breadcrumb "Dashboard / Profil Admin"; diakses via dropdown avatar topbar |
| UX Flow | Buka via avatar dropdown → ubah data/foto → simpan → notifikasi; atau isi form password → validasi → simpan |

---

## 11. Sitemap Aplikasi

```
Login
 └─ (redirect sesuai role setelah autentikasi)
     ├─ ADMIN
     │   └─ Dashboard (Admin)
     │       ├─ Data Karyawan
     │       ├─ Data Jabatan
     │       ├─ Data Potongan
     │       ├─ Data Penggajian
     │       │   └─ Detail Slip Gaji
     │       ├─ Laporan
     │       └─ Profil Admin
     └─ KARYAWAN
         └─ Dashboard (Karyawan)
             ├─ Riwayat Penggajian
             │   └─ Detail Slip Gaji (milik sendiri)
             └─ Profil Karyawan
```

## 12. Daftar Komponen UI

| Komponen | Fungsi | Dipakai di Halaman |
|---|---|---|
| Topbar | Info user, notifikasi, dropdown akun | Semua halaman setelah login |
| Sidebar Navigasi | Menu utama, collapsible | Semua halaman Admin |
| Breadcrumb | Penunjuk lokasi halaman | Semua halaman internal |
| Card Statistik | Ringkasan angka | Dashboard |
| Card Slip Gaji | Struktur tampilan slip gaji | Detail Slip Gaji |
| Tabel Data + Pagination | Tampilkan data terstruktur | Karyawan, Jabatan, Potongan, Penggajian, Laporan |
| Badge Status | Indikator aktif/nonaktif/draft/final/dibayar | Karyawan, Potongan, Penggajian |
| Search Bar | Cari data | Data Karyawan |
| Filter Dropdown | Filter jabatan/status/periode | Karyawan, Penggajian, Laporan |
| Date Picker | Pilih periode | Data Penggajian, Laporan |
| Toggle Switch | Aktif/nonaktif cepat | Data Potongan |
| Tombol (Button) | Aksi primer/sekunder/bahaya | Semua halaman |
| Modal / Side Panel | Form tambah/edit tanpa pindah halaman | Karyawan, Jabatan, Potongan |
| Form Input | Input data (text/number/select) | Login, semua form CRUD, Profil |
| Avatar / Upload Foto | Foto profil pengguna | Profil Admin, Topbar |
| Alert / Toast Notifikasi | Feedback sukses/gagal | Semua halaman setelah aksi |
| Grafik (Chart) | Visualisasi tren gaji | Dashboard, Laporan |
| Tombol Unduh/Ekspor | Unduh PDF/Excel | Detail Slip Gaji, Laporan |

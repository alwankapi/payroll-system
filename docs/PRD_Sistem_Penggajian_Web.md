# Product Requirements Document (PRD)
## Sistem Informasi Penggajian Berbasis Web

| | |
|---|---|
| **Nama Proyek** | Sistem Penggajian (Payroll Web System) |
| **Versi Dokumen** | 1.0 |
| **Tanggal** | 23 Juli 2026 |
| **Disusun oleh** | Senior System Analyst |
| **Framework** | Laravel 12, PHP 8.3, MySQL, Tailwind CSS, Laravel Breeze |

---

## Daftar Isi
1. [Latar Belakang](#1-latar-belakang)
2. [Tujuan](#2-tujuan)
3. [Scope](#3-scope)
4. [Functional Requirements](#4-functional-requirements)
5. [Non Functional Requirements](#5-non-functional-requirements)
6. [User Story](#6-user-story)
7. [Acceptance Criteria](#7-acceptance-criteria)
8. [Business Rules](#8-business-rules)
9. [Tech Stack](#9-tech-stack)
10. [Deliverables](#10-deliverables)

---

## 1. Latar Belakang

Proses penggajian pada banyak instansi/organisasi skala kecil-menengah masih dilakukan secara manual atau semi-manual menggunakan spreadsheet. Pendekatan ini menimbulkan sejumlah masalah: rawan kesalahan hitung, waktu proses yang lama setiap periode gaji, data jabatan/karyawan/potongan yang tersebar dan tidak konsisten, sulitnya menelusuri riwayat penggajian, serta minimnya transparansi bagi karyawan terhadap rincian gaji yang mereka terima.

Proyek ini sebelumnya telah diimplementasikan sebagai aplikasi PHP native (PDO) untuk memenuhi kompetensi dasar analis sistem (query SQL, akses basis data, implementasi algoritma). Tahap berikutnya adalah membangun ulang sistem tersebut sebagai **aplikasi web berbasis Laravel** yang terstruktur, aman, dan mudah dikembangkan — mencakup autentikasi, manajemen data master (jabatan, karyawan, potongan), perhitungan gaji otomatis, slip gaji digital, serta pelaporan penggajian, dengan pemisahan hak akses antara Admin dan Karyawan.

## 2. Tujuan

- Mengotomatiskan perhitungan gaji karyawan agar lebih cepat, akurat, dan meminimalkan kesalahan manusia.
- Menyediakan basis data terpusat untuk data jabatan, karyawan, dan jenis potongan.
- Mempermudah pembuatan slip gaji dalam format PDF secara otomatis dan konsisten.
- Menyediakan laporan penggajian periodik yang dapat diakses kapan saja oleh Admin.
- Menerapkan hak akses berbasis role (Admin dan Karyawan) sehingga setiap pengguna hanya mengakses data sesuai kewenangannya.
- Meningkatkan transparansi karyawan terhadap rincian gaji yang diterima setiap periode.
- Menjaga keamanan dan konsistensi data penggajian yang bersifat rahasia.
- Memenuhi kompetensi mata kuliah/penilaian analis sistem: penggunaan SQL, akses basis data, implementasi algoritma, dokumentasi kode, debugging, code review, unit testing, dan integration testing yang siap dipresentasikan ke asesor.

## 3. Scope

### 3.1 Dalam Lingkup (In-Scope)
- Autentikasi (Login) menggunakan Laravel Breeze.
- Dashboard untuk role Admin dan Karyawan.
- CRUD data Jabatan.
- CRUD data Karyawan.
- CRUD data Potongan.
- CRUD data Penggajian.
- Perhitungan gaji otomatis berdasarkan gaji pokok jabatan dan potongan yang berlaku.
- Pembuatan slip gaji dalam format PDF.
- Laporan penggajian periodik.
- Manajemen hak akses berbasis role (Admin & Karyawan).

### 3.2 Di Luar Lingkup (Out of Scope)
- Integrasi sistem absensi/presensi (fingerprint, GPS, dsb).
- Integrasi pembayaran otomatis ke rekening bank (payment gateway/host-to-host banking).
- Pelaporan pajak PPh21 ke sistem DJP/e-Filing.
- Modul multi-perusahaan/multi-cabang.
- Aplikasi mobile native (Android/iOS).
- Approval workflow berjenjang (multi-level approval) untuk finalisasi gaji.
- Manajemen cuti/izin karyawan.

### 3.3 Asumsi
- Jenis dan nilai potongan (mis. BPJS Kesehatan, BPJS Ketenagakerjaan, PPh21, keterlambatan) bersifat data master yang diinput dan diubah bebas oleh Admin, bukan nilai yang di-hardcode.
- Satu jabatan memiliki satu nilai gaji pokok dan tunjangan jabatan yang berlaku untuk seluruh karyawan pada jabatan tersebut.

## 4. Functional Requirements

### 4.1 Autentikasi (Login)
| ID | Deskripsi |
|---|---|
| FR-01 | Sistem menyediakan halaman login (Laravel Breeze) untuk seluruh pengguna. |
| FR-02 | Sistem memvalidasi email & password, menampilkan pesan error jika kredensial tidak valid. |
| FR-03 | Setelah login berhasil, sistem mengarahkan pengguna ke dashboard sesuai role (Admin/Karyawan). |
| FR-04 | Sistem menyediakan fitur logout. |
| FR-05 | Tidak ada halaman registrasi publik; akun pengguna hanya dibuat oleh Admin dan otomatis terhubung ke data Karyawan terkait. |

### 4.2 Dashboard
| ID | Deskripsi |
|---|---|
| FR-06 | Dashboard Admin menampilkan ringkasan: jumlah karyawan aktif, jumlah jabatan, total pengeluaran gaji periode berjalan, status penggajian terbaru. |
| FR-07 | Dashboard Karyawan menampilkan ringkasan slip gaji periode terakhir dan riwayat penggajian singkat milik karyawan yang login. |

### 4.3 CRUD Jabatan
| ID | Deskripsi |
|---|---|
| FR-08 | Admin dapat menambah jabatan baru (nama jabatan, gaji pokok, tunjangan jabatan, keterangan). |
| FR-09 | Admin dapat melihat daftar seluruh jabatan beserta gaji pokok & tunjangan. |
| FR-10 | Admin dapat mengubah data jabatan. |
| FR-11 | Admin dapat menghapus jabatan, dengan validasi jabatan yang masih dipakai karyawan aktif tidak dapat dihapus. |

### 4.4 CRUD Karyawan
| ID | Deskripsi |
|---|---|
| FR-12 | Admin dapat menambah karyawan baru (NIK, nama lengkap, jabatan, alamat, no. telepon, email, tanggal masuk, no. rekening, status aktif/nonaktif). |
| FR-13 | Admin dapat melihat daftar karyawan dengan fitur pencarian & filter status/jabatan. |
| FR-14 | Admin dapat mengubah data karyawan. |
| FR-15 | Admin dapat menonaktifkan karyawan tanpa menghapus riwayat penggajiannya. |
| FR-16 | Setiap data karyawan terhubung ke satu akun user untuk login. |

### 4.5 CRUD Potongan
| ID | Deskripsi |
|---|---|
| FR-17 | Admin dapat menambah jenis potongan baru (nama, jenis perhitungan [persentase/nominal tetap], nilai, status aktif). |
| FR-18 | Admin dapat melihat daftar seluruh jenis potongan. |
| FR-19 | Admin dapat mengubah data potongan. |
| FR-20 | Admin dapat menonaktifkan potongan; data potongan yang sudah pernah dipakai di penggajian tetap tersimpan sebagai riwayat. |

### 4.6 CRUD Penggajian
| ID | Deskripsi |
|---|---|
| FR-21 | Admin dapat membuat data penggajian untuk satu karyawan atau seluruh karyawan aktif dalam satu periode (bulan/tahun). |
| FR-22 | Sistem menampilkan rincian per karyawan: gaji pokok, tunjangan, rincian potongan yang diterapkan, dan gaji bersih. |
| FR-23 | Admin dapat mengubah status penggajian (Draft, Final, Dibayar). |
| FR-24 | Data penggajian berstatus Draft dapat dihapus; status Final/Dibayar tidak dapat dihapus. |
| FR-25 | Sistem mencegah duplikasi data penggajian untuk kombinasi karyawan + periode yang sama. |

### 4.7 Perhitungan Gaji Otomatis
| ID | Deskripsi |
|---|---|
| FR-26 | Sistem menghitung gaji bersih otomatis: **Gaji Bersih = Gaji Pokok + Tunjangan Jabatan − Total Potongan**. |
| FR-27 | Total potongan dihitung dari seluruh potongan aktif yang berlaku, sesuai jenisnya (persentase dari gaji pokok atau nominal tetap). |
| FR-28 | Admin dapat menjalankan proses "Generate Penggajian" untuk seluruh karyawan aktif dalam satu periode sekaligus (bulk generate). |
| FR-29 | Sistem menyimpan snapshot nilai gaji pokok, tunjangan, dan tiap potongan pada saat penggajian dibuat, sehingga riwayat gaji tidak berubah walau data master jabatan/potongan diubah kemudian. |

### 4.8 Slip Gaji PDF
| ID | Deskripsi |
|---|---|
| FR-30 | Sistem men-generate slip gaji PDF per karyawan per periode berstatus Final/Dibayar. |
| FR-31 | Slip gaji memuat: identitas perusahaan, identitas karyawan (nama, NIK, jabatan), periode, rincian pendapatan, rincian potongan, dan gaji bersih. |
| FR-32 | Karyawan dapat mengunduh slip gajinya sendiri melalui dashboard/riwayat penggajian. |
| FR-33 | Admin dapat mengunduh/mencetak slip gaji seluruh karyawan atau per karyawan. |

### 4.9 Laporan Penggajian
| ID | Deskripsi |
|---|---|
| FR-34 | Sistem menyediakan laporan rekap penggajian per periode dengan total gaji bersih seluruh karyawan. |
| FR-35 | Admin dapat memfilter laporan berdasarkan periode, jabatan, atau status penggajian. |
| FR-36 | Laporan dapat diekspor ke format PDF (opsional Excel). |
| FR-37 | Laporan menampilkan ringkasan total pengeluaran gaji perusahaan pada periode terpilih. |

### 4.10 Role Admin & Karyawan
| ID | Deskripsi |
|---|---|
| FR-38 | Sistem membedakan hak akses berdasarkan role menggunakan middleware. |
| FR-39 | Role Admin memiliki akses penuh ke seluruh modul. |
| FR-40 | Role Karyawan hanya mengakses dashboard, profil, dan slip gaji/riwayat penggajian miliknya sendiri. |
| FR-41 | Sistem menolak akses (403/redirect) bila pengguna mengakses modul di luar kewenangan role-nya. |

## 5. Non Functional Requirements

| ID | Kategori | Deskripsi |
|---|---|---|
| NFR-01 | Security | Password di-hash (bcrypt) via Laravel Breeze; seluruh form dilindungi CSRF token. |
| NFR-02 | Security | Akses data gaji dibatasi sesuai role; data sensitif tidak dapat diakses pengguna tidak berwenang. |
| NFR-03 | Performance | Waktu respon halaman CRUD ≤ 2 detik pada kondisi normal (hingga ±1000 data karyawan). |
| NFR-04 | Performance | Proses generate penggajian massal untuk seluruh karyawan aktif selesai dalam waktu wajar (< 1 menit untuk 500 karyawan). |
| NFR-05 | Usability | Antarmuka Tailwind CSS, responsif di desktop, tablet, dan mobile. |
| NFR-06 | Usability | Bahasa antarmuka Indonesia; format mata uang Rupiah (Rp) dan format tanggal Indonesia. |
| NFR-07 | Reliability | Validasi input dilakukan di sisi server (Laravel Form Request) untuk mencegah data tidak valid tersimpan. |
| NFR-08 | Maintainability | Kode mengikuti struktur MVC Laravel & PSR coding standard, disertai dokumentasi kode (docblock/README) untuk keperluan penilaian akademik. |
| NFR-09 | Testability | Fungsi utama (perhitungan gaji, CRUD inti) disertai unit test dan integration test agar dapat diverifikasi dan dipresentasikan ke asesor. |
| NFR-10 | Scalability | Struktur database dirancang menampung pertumbuhan data karyawan & histori penggajian tanpa perubahan besar pada arsitektur. |
| NFR-11 | Compatibility | Berjalan baik pada browser modern (Chrome, Firefox, Edge, Safari versi terbaru). |
| NFR-12 | Data Integrity | Relasi antar tabel (Jabatan-Karyawan-Potongan-Penggajian) memakai foreign key constraint. |
| NFR-13 | Availability | Target uptime minimal 99% pada lingkungan produksi/demo. |

## 6. User Story

| ID | Sebagai | Saya ingin | Agar |
|---|---|---|---|
| US-01 | Admin | login ke sistem dengan aman | dapat mengakses seluruh modul penggajian |
| US-02 | Admin | mengelola data jabatan | struktur gaji pokok per posisi selalu akurat |
| US-03 | Admin | mengelola data karyawan | data kepegawaian selalu up to date |
| US-04 | Admin | mengelola jenis potongan | perhitungan potongan gaji sesuai ketentuan |
| US-05 | Admin | menjalankan perhitungan gaji otomatis | tidak perlu hitung manual dan mengurangi risiko salah hitung |
| US-06 | Admin | mengelola data penggajian per periode | dapat meninjau & memfinalisasi gaji sebelum dibayarkan |
| US-07 | Admin | mencetak/mengunduh slip gaji karyawan | karyawan memiliki bukti penerimaan gaji resmi |
| US-08 | Admin | melihat & mengekspor laporan penggajian | dapat melaporkan pengeluaran gaji ke pihak manajemen |
| US-09 | Karyawan | login ke sistem | dapat melihat informasi gaji milik saya sendiri |
| US-10 | Karyawan | melihat dashboard pribadi | mengetahui ringkasan gaji periode terakhir |
| US-11 | Karyawan | mengunduh slip gaji saya sendiri | memiliki dokumen resmi terkait pendapatan saya |
| US-12 | Karyawan | melihat riwayat penggajian saya | dapat memantau pendapatan dari waktu ke waktu |

## 7. Acceptance Criteria

**AC-01 — Login**
- Given akun terdaftar dengan email & password valid, When mengisi form login dengan benar, Then diarahkan ke dashboard sesuai role.
- Given email/password salah, When submit, Then muncul pesan error, akses ditolak.

**AC-02 — CRUD Jabatan**
- Given Admin di halaman Jabatan, When mengisi form tambah dengan data valid, Then data tersimpan dan tampil di daftar.
- Given jabatan masih dipakai karyawan aktif, When Admin coba hapus, Then sistem menolak dan menampilkan peringatan.

**AC-03 — CRUD Karyawan**
- Given seluruh field wajib terisi valid, When Admin submit form tambah karyawan, Then data tersimpan beserta akun login otomatis dibuat.
- Given karyawan dinonaktifkan, When status diubah nonaktif, Then karyawan tidak diikutkan pada generate penggajian periode berikutnya, riwayat sebelumnya tetap ada.

**AC-04 — CRUD Potongan**
- Given jenis potongan "persentase" bernilai 2%, When potongan diterapkan pada penggajian, Then sistem menghitung potongan sebesar 2% dari gaji pokok karyawan terkait.

**AC-05 — Penggajian & Perhitungan Otomatis**
- Given periode belum pernah digenerate untuk karyawan tertentu, When Admin generate, Then data penggajian terbentuk dengan gaji pokok, tunjangan, potongan, dan gaji bersih terhitung otomatis.
- Given data penggajian karyawan+periode sudah ada, When Admin generate ulang, Then sistem mencegah duplikasi dan menampilkan peringatan.
- Given status penggajian "Final", When Admin coba edit nilai gaji, Then sistem menolak atau meminta buka-kembali status secara eksplisit.

**AC-06 — Slip Gaji PDF**
- Given penggajian berstatus Final, When karyawan/Admin klik "Unduh Slip Gaji", Then sistem menghasilkan PDF berisi rincian gaji sesuai periode tersebut.

**AC-07 — Laporan Penggajian**
- Given Admin memilih periode & filter tertentu, When klik "Tampilkan/Export", Then sistem menampilkan/mengekspor rekap sesuai filter.

**AC-08 — Role Access**
- Given login sebagai Karyawan, When mencoba akses URL modul Admin, Then sistem menolak akses (403/redirect).

## 8. Business Rules

| ID | Aturan |
|---|---|
| BR-01 | Hanya role Admin yang dapat create/update/delete data Jabatan, Karyawan, dan Potongan. |
| BR-02 | Role Karyawan hanya memiliki akses baca terhadap data miliknya sendiri (profil, slip gaji, riwayat). |
| BR-03 | Rumus gaji bersih: Gaji Bersih = Gaji Pokok + Tunjangan Jabatan − Total Potongan. |
| BR-04 | Potongan "Persentase" dihitung dari Gaji Pokok; potongan "Nominal Tetap" dihitung sebagai nilai tetap. |
| BR-05 | Satu karyawan hanya memiliki satu data penggajian untuk satu periode (bulan-tahun) yang sama. |
| BR-06 | Penggajian berstatus Final/Dibayar tidak dapat dihapus, hanya dapat diubah lewat mekanisme reopen oleh Admin. |
| BR-07 | Slip gaji hanya dapat digenerate untuk penggajian berstatus Final/Dibayar. |
| BR-08 | Karyawan berstatus Nonaktif tidak diikutkan pada generate penggajian periode berikutnya. |
| BR-09 | Jabatan yang masih direferensikan karyawan aktif tidak dapat dihapus. |
| BR-10 | Perubahan data master Jabatan/Potongan tidak memengaruhi data penggajian yang sudah dibuat sebelumnya (bersifat snapshot/historis). |
| BR-11 | Akun login hanya dibuat oleh Admin dan terhubung ke satu data Karyawan. |

## 9. Tech Stack

| Kategori | Teknologi |
|---|---|
| Backend Framework | Laravel 12 |
| Bahasa Pemrograman | PHP 8.3 |
| Database | MySQL |
| Frontend Styling | Tailwind CSS |
| Autentikasi | Laravel Breeze |
| ORM | Eloquent |
| Template Engine | Blade |
| PDF Generator | barryvdh/laravel-dompdf (rekomendasi) |
| Export Excel (opsional) | maatwebsite/excel |
| Version Control | Git & GitHub |
| Dev Environment | Laravel Herd / Laragon / XAMPP (rekomendasi lokal) |

## 10. Deliverables

- Dokumen PRD ini.
- Entity Relationship Diagram (ERD).
- Use Case Diagram.
- Source code aplikasi (repository Laravel).
- File migrasi & seeder database.
- Data dictionary / dokumentasi struktur database.
- Aplikasi berjalan mencakup seluruh fitur: Login, Dashboard, CRUD Jabatan/Karyawan/Potongan/Penggajian, Perhitungan Otomatis, Slip Gaji PDF, Laporan Penggajian, Role Admin & Karyawan.
- Contoh slip gaji (sample PDF).
- Dokumentasi kode (docblock/README) untuk keperluan penilaian.
- Laporan unit testing & integration testing.
- Panduan penggunaan aplikasi (user manual) untuk Admin dan Karyawan.
- Panduan instalasi/deployment.
- Bahan presentasi untuk asesor/dosen penguji.

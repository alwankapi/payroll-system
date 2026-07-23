# Rancangan Database
## Sistem Informasi Penggajian Berbasis Web

| | |
|---|---|
| **Nama Proyek** | Sistem Penggajian (Payroll Web System) |
| **Versi Dokumen** | 1.0 |
| **Tanggal** | 23 Juli 2026 |
| **Disusun oleh** | Database Architect |
| **Referensi** | PRD v1.0, Dokumen Analisis Sistem v1.0 |
| **DBMS** | MySQL |

---

## 1. ERD

| Entitas | Primary Key | Atribut Lain | Foreign Key |
|---|---|---|---|
| users | id | name, email, email_verified_at, password, role, remember_token, created_at, updated_at | - |
| jabatans | id | nama_jabatan, gaji_pokok, tunjangan_jabatan, keterangan, created_at, updated_at | - |
| karyawans | id | nik, nama_lengkap, alamat, no_telepon, tanggal_masuk, no_rekening, status_karyawan, created_at, updated_at | user_id → users.id, jabatan_id → jabatans.id |
| potongans | id | nama_potongan, jenis_potongan, nilai, status_aktif, created_at, updated_at | - |
| penggajians | id | periode, gaji_pokok, tunjangan, total_potongan, gaji_bersih, status, tanggal_bayar, created_at, updated_at | karyawan_id → karyawans.id |
| penggajian_detail | id | nama_potongan, nilai_potongan, created_at, updated_at | penggajian_id → penggajians.id, potongan_id → potongans.id |

## 2. Relasi

| Entitas A | Relasi | Entitas B | Kardinalitas | Keterangan |
|---|---|---|---|---|
| users | memiliki | karyawans | 1 : 1 | Satu akun login terhubung ke satu data karyawan |
| jabatans | memiliki | karyawans | 1 : N | Satu jabatan dipakai banyak karyawan |
| karyawans | memiliki | penggajians | 1 : N | Satu karyawan punya banyak riwayat penggajian |
| penggajians | memiliki | penggajian_detail | 1 : N | Satu penggajian punya banyak rincian potongan |
| potongans | dipakai di | penggajian_detail | 1 : N | Satu jenis potongan dipakai di banyak rincian penggajian |

## 3. Normalisasi sampai 3NF

| Tabel | 1NF | 2NF | 3NF | Catatan |
|---|---|---|---|---|
| users | Terpenuhi | Terpenuhi | Terpenuhi | PK tunggal (id), tidak ada dependensi transitif |
| jabatans | Terpenuhi | Terpenuhi | Terpenuhi | gaji_pokok & tunjangan melekat langsung ke jabatan |
| karyawans | Terpenuhi | Terpenuhi | Terpenuhi | gaji_pokok TIDAK disimpan di sini — hanya jabatan_id, agar tidak transitif lewat jabatan |
| potongans | Terpenuhi | Terpenuhi | Terpenuhi | - |
| penggajians | Terpenuhi | Terpenuhi | Denormalisasi terkontrol | gaji_pokok/tunjangan/total_potongan/gaji_bersih adalah nilai snapshot (turunan). Sengaja redundan demi riwayat historis (BR-10) — bukan pelanggaran 3NF murni, melainkan keputusan desain disengaja |
| penggajian_detail | Terpenuhi | Terpenuhi | Denormalisasi terkontrol | nama_potongan disimpan sebagai snapshot agar slip gaji lama tidak berubah bila master potongan diedit/dihapus |

## 4. Data Dictionary

**users**

| Kolom | Tipe Data | Panjang | Null | Default | Keterangan |
|---|---|---|---|---|---|
| id | BIGINT | - | No | auto_increment | Primary key |
| name | VARCHAR | 100 | No | - | Nama pengguna |
| email | VARCHAR | 100 | No | - | Email login, unik |
| email_verified_at | TIMESTAMP | - | Yes | NULL | Verifikasi email (Breeze) |
| password | VARCHAR | 255 | No | - | Password ter-hash |
| role | ENUM('admin','karyawan') | - | No | 'karyawan' | Peran pengguna |
| remember_token | VARCHAR | 100 | Yes | NULL | Token remember-me |
| created_at / updated_at | TIMESTAMP | - | Yes | NULL | Timestamp Laravel |

**jabatans**

| Kolom | Tipe Data | Panjang | Null | Default | Keterangan |
|---|---|---|---|---|---|
| id | BIGINT | - | No | auto_increment | Primary key |
| nama_jabatan | VARCHAR | 100 | No | - | Nama jabatan, unik |
| gaji_pokok | DECIMAL | 15,2 | No | 0 | Gaji pokok jabatan |
| tunjangan_jabatan | DECIMAL | 15,2 | No | 0 | Tunjangan tetap jabatan |
| keterangan | TEXT | - | Yes | NULL | Deskripsi jabatan |
| created_at / updated_at | TIMESTAMP | - | Yes | NULL | Timestamp Laravel |

**karyawans**

| Kolom | Tipe Data | Panjang | Null | Default | Keterangan |
|---|---|---|---|---|---|
| id | BIGINT | - | No | auto_increment | Primary key |
| user_id | BIGINT | - | No | - | FK → users.id, unik (1:1) |
| jabatan_id | BIGINT | - | No | - | FK → jabatans.id |
| nik | VARCHAR | 20 | No | - | Nomor induk kependudukan, unik |
| nama_lengkap | VARCHAR | 100 | No | - | Nama karyawan |
| alamat | TEXT | - | Yes | NULL | Alamat domisili |
| no_telepon | VARCHAR | 20 | Yes | NULL | Nomor telepon |
| tanggal_masuk | DATE | - | No | - | Tanggal mulai kerja |
| no_rekening | VARCHAR | 30 | Yes | NULL | Nomor rekening gaji |
| status_karyawan | ENUM('aktif','nonaktif') | - | No | 'aktif' | Status kepegawaian |
| created_at / updated_at | TIMESTAMP | - | Yes | NULL | Timestamp Laravel |

**potongans**

| Kolom | Tipe Data | Panjang | Null | Default | Keterangan |
|---|---|---|---|---|---|
| id | BIGINT | - | No | auto_increment | Primary key |
| nama_potongan | VARCHAR | 100 | No | - | Nama jenis potongan |
| jenis_potongan | ENUM('persentase','nominal') | - | No | - | Cara hitung potongan |
| nilai | DECIMAL | 15,2 | No | 0 | Nilai persentase/nominal |
| status_aktif | BOOLEAN | - | No | true | Status aktif potongan |
| created_at / updated_at | TIMESTAMP | - | Yes | NULL | Timestamp Laravel |

**penggajians**

| Kolom | Tipe Data | Panjang | Null | Default | Keterangan |
|---|---|---|---|---|---|
| id | BIGINT | - | No | auto_increment | Primary key |
| karyawan_id | BIGINT | - | No | - | FK → karyawans.id |
| periode | DATE | - | No | - | Periode gaji (tgl 1 bulan berjalan) |
| gaji_pokok | DECIMAL | 15,2 | No | 0 | Snapshot gaji pokok |
| tunjangan | DECIMAL | 15,2 | No | 0 | Snapshot tunjangan |
| total_potongan | DECIMAL | 15,2 | No | 0 | Snapshot total potongan |
| gaji_bersih | DECIMAL | 15,2 | No | 0 | Hasil akhir perhitungan |
| status | ENUM('draft','final','dibayar') | - | No | 'draft' | Status penggajian |
| tanggal_bayar | DATE | - | Yes | NULL | Tanggal pembayaran aktual |
| created_at / updated_at | TIMESTAMP | - | Yes | NULL | Timestamp Laravel |

**penggajian_detail**

| Kolom | Tipe Data | Panjang | Null | Default | Keterangan |
|---|---|---|---|---|---|
| id | BIGINT | - | No | auto_increment | Primary key |
| penggajian_id | BIGINT | - | No | - | FK → penggajians.id |
| potongan_id | BIGINT | - | No | - | FK → potongans.id |
| nama_potongan | VARCHAR | 100 | No | - | Snapshot nama potongan |
| nilai_potongan | DECIMAL | 15,2 | No | 0 | Snapshot nilai potongan terhitung |
| created_at / updated_at | TIMESTAMP | - | Yes | NULL | Timestamp Laravel |

## 5. Primary Key

| Tabel | Primary Key |
|---|---|
| users | id |
| jabatans | id |
| karyawans | id |
| potongans | id |
| penggajians | id |
| penggajian_detail | id |

## 6. Foreign Key

| Tabel | Kolom FK | Referensi | On Update | On Delete |
|---|---|---|---|---|
| karyawans | user_id | users.id | CASCADE | CASCADE |
| karyawans | jabatan_id | jabatans.id | CASCADE | RESTRICT |
| penggajians | karyawan_id | karyawans.id | CASCADE | RESTRICT |
| penggajian_detail | penggajian_id | penggajians.id | CASCADE | CASCADE |
| penggajian_detail | potongan_id | potongans.id | CASCADE | RESTRICT |

*RESTRICT dipasang pada jabatan_id, karyawan_id, potongan_id agar data yang masih dipakai/riwayat tidak bisa terhapus tak sengaja (selaras BR-09 & FR-20).*

## 7. Constraint

| Tabel | Kolom | Constraint |
|---|---|---|
| users | email | UNIQUE, NOT NULL |
| users | role | CHECK IN ('admin','karyawan') |
| jabatans | nama_jabatan | UNIQUE, NOT NULL |
| jabatans | gaji_pokok | CHECK (>= 0) |
| karyawans | user_id | UNIQUE, NOT NULL |
| karyawans | nik | UNIQUE, NOT NULL |
| karyawans | status_karyawan | CHECK IN ('aktif','nonaktif') |
| potongans | jenis_potongan | CHECK IN ('persentase','nominal') |
| potongans | nilai | CHECK (>= 0) |
| penggajians | (karyawan_id, periode) | UNIQUE composite — cegah duplikasi (BR-05) |
| penggajians | status | CHECK IN ('draft','final','dibayar') |
| penggajians | gaji_bersih | CHECK (>= 0) |
| penggajian_detail | nilai_potongan | CHECK (>= 0) |

## 8. Index

| Tabel | Kolom | Jenis Index | Alasan |
|---|---|---|---|
| users | email | UNIQUE INDEX | Pencarian saat login |
| karyawans | jabatan_id | INDEX | Filter/join berdasar jabatan |
| karyawans | status_karyawan | INDEX | Filter karyawan aktif/nonaktif |
| karyawans | nik | UNIQUE INDEX | Pencarian cepat berdasar NIK |
| potongans | status_aktif | INDEX | Filter potongan aktif saat generate gaji |
| penggajians | (karyawan_id, periode) | UNIQUE INDEX | Cegah duplikasi + cari cepat riwayat karyawan |
| penggajians | periode | INDEX | Filter laporan per periode |
| penggajians | status | INDEX | Filter status saat laporan/slip gaji |
| penggajian_detail | penggajian_id | INDEX | Join cepat ke rincian slip gaji |

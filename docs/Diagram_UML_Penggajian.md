# Diagram UML
## Sistem Informasi Penggajian Berbasis Web

| | |
|---|---|
| **Nama Proyek** | Sistem Penggajian (Payroll Web System) |
| **Versi Dokumen** | 1.0 |
| **Tanggal** | 23 Juli 2026 |
| **Disusun oleh** | Software Architect |
| **Referensi** | PRD v1.0, Analisis Sistem v1.0, Rancangan Database v1.0 |
| **Notasi** | PlantUML |

Seluruh diagram di bawah pakai penamaan aktor, entitas, dan alur yang sama dengan dokumen-dokumen sebelumnya (Admin, Karyawan, jabatans, karyawans, potongans, penggajians, penggajian_detail). Kode bisa langsung dirender lewat plantuml.com/plantuml atau ekstensi PlantUML di VS Code.

---

## Daftar Isi
1. Use Case Diagram
2. Activity Diagram
3. Sequence Diagram
4. Class Diagram
5. Deployment Diagram

---

## 1. Use Case Diagram

```plantuml
@startuml UseCase_SistemPenggajian
left to right direction
skinparam packageStyle rectangle

actor Pengguna
actor Admin
actor Karyawan

Admin --|> Pengguna
Karyawan --|> Pengguna

rectangle "Sistem Penggajian" {
  usecase "Login" as UC1
  usecase "Logout" as UC2
  usecase "Kelola Data Jabatan" as UC3
  usecase "Kelola Data Karyawan" as UC4
  usecase "Kelola Data Potongan" as UC5
  usecase "Generate Penggajian" as UC6
  usecase "Hitung Gaji Otomatis" as UC7
  usecase "Kelola Status Penggajian" as UC8
  usecase "Cetak / Unduh Slip Gaji" as UC9
  usecase "Lihat Laporan Penggajian" as UC10
  usecase "Lihat Dashboard Admin" as UC11
  usecase "Lihat Dashboard Karyawan" as UC12
  usecase "Lihat Riwayat Penggajian Sendiri" as UC13
  usecase "Unduh Slip Gaji Sendiri" as UC14
}

Pengguna -- UC1
Pengguna -- UC2

Admin -- UC3
Admin -- UC4
Admin -- UC5
Admin -- UC6
Admin -- UC8
Admin -- UC9
Admin -- UC10
Admin -- UC11

Karyawan -- UC12
Karyawan -- UC13
Karyawan -- UC14

UC6 ..> UC7 : <<include>>
UC9 ..> UC8 : <<extend>>
UC14 ..> UC13 : <<extend>>
@enduml
```

## 2. Activity Diagram

Alur proses generate penggajian satu periode, sampai slip gaji diunduh karyawan.

```plantuml
@startuml Activity_ProsesPenggajian
|Admin|
start
:Pastikan data Jabatan & Karyawan aktif valid;
:Pastikan data Potongan aktif sesuai ketentuan;
:Jalankan Generate Penggajian untuk periode berjalan;

|Sistem|
:Ambil gaji pokok & tunjangan dari Jabatan;
:Hitung total potongan aktif per karyawan;
:Hitung gaji bersih (gaji pokok + tunjangan - total potongan);
if (Karyawan + periode sudah pernah digenerate?) then (ya)
  :Tolak, tampilkan peringatan duplikasi;
  stop
else (tidak)
  :Simpan data Penggajian status Draft;
endif

|Admin|
:Tinjau / verifikasi data penggajian Draft;
if (Data sudah benar?) then (ya)
  :Ubah status jadi Final;
else (tidak)
  :Perbaiki data lalu generate ulang;
  stop
endif

|Sistem|
:Kunci data Final dari perubahan langsung;

|Admin|
:Proses pembayaran gaji (di luar sistem);
:Ubah status jadi Dibayar;

|Sistem|
:Aktifkan unduh Slip Gaji PDF;

|Karyawan|
:Login ke sistem;
:Buka dashboard pribadi;
:Unduh slip gaji sendiri;
stop
@enduml
```

## 3. Sequence Diagram

**3.1 Generate Penggajian**

```plantuml
@startuml Sequence_GeneratePenggajian
actor Admin
participant "PenggajianController" as Ctrl
participant "Karyawan" as MK
participant "Jabatan" as MJ
participant "Potongan" as MP
database "Database" as DB

Admin -> Ctrl : generatePenggajian(periode)
Ctrl -> MK : getKaryawanAktif()
MK -> DB : SELECT * FROM karyawans WHERE status_karyawan='aktif'
DB --> MK : list karyawan aktif
MK --> Ctrl : list karyawan aktif

loop untuk setiap karyawan
  Ctrl -> MJ : getGajiPokok(jabatan_id)
  MJ -> DB : SELECT gaji_pokok, tunjangan_jabatan FROM jabatans WHERE id=?
  DB --> MJ : gaji_pokok, tunjangan_jabatan
  MJ --> Ctrl : gaji_pokok, tunjangan_jabatan

  Ctrl -> MP : getPotonganAktif()
  MP -> DB : SELECT * FROM potongans WHERE status_aktif=true
  DB --> MP : list potongan aktif
  MP --> Ctrl : list potongan aktif

  Ctrl -> Ctrl : hitungTotalPotongan()
  Ctrl -> Ctrl : hitungGajiBersih()

  alt kombinasi karyawan+periode belum ada
    Ctrl -> DB : INSERT INTO penggajians (...)
    DB --> Ctrl : id penggajian baru
    Ctrl -> DB : INSERT INTO penggajian_detail (...)
  else sudah ada
    Ctrl --> Admin : peringatan duplikasi
  end
end

Ctrl --> Admin : hasil generate penggajian
@enduml
```

**3.2 Unduh Slip Gaji**

```plantuml
@startuml Sequence_UnduhSlipGaji
actor Karyawan
participant "SlipGajiController" as Ctrl
participant "Penggajian" as MPG
participant "PDF Generator" as PDF

Karyawan -> Ctrl : unduhSlipGaji(penggajian_id)
Ctrl -> MPG : getPenggajian(penggajian_id)
MPG --> Ctrl : data penggajian

alt status Final atau Dibayar
  Ctrl -> PDF : generate(dataPenggajian)
  PDF --> Ctrl : file PDF
  Ctrl --> Karyawan : unduh file slip gaji
else status Draft
  Ctrl --> Karyawan : slip gaji belum tersedia
end
@enduml
```

## 4. Class Diagram

```plantuml
@startuml Class_SistemPenggajian
class User {
  +id: bigint
  +name: string
  +email: string
  +password: string
  +role: string
  --
  +karyawan(): Karyawan
}

class Jabatan {
  +id: bigint
  +nama_jabatan: string
  +gaji_pokok: decimal
  +tunjangan_jabatan: decimal
  +keterangan: string
  --
  +karyawans(): Karyawan[]
}

class Karyawan {
  +id: bigint
  +user_id: bigint
  +jabatan_id: bigint
  +nik: string
  +nama_lengkap: string
  +alamat: string
  +no_telepon: string
  +tanggal_masuk: date
  +no_rekening: string
  +status_karyawan: string
  --
  +user(): User
  +jabatan(): Jabatan
  +penggajians(): Penggajian[]
}

class Potongan {
  +id: bigint
  +nama_potongan: string
  +jenis_potongan: string
  +nilai: decimal
  +status_aktif: boolean
  --
  +hitungPotongan(gajiPokok: decimal): decimal
}

class Penggajian {
  +id: bigint
  +karyawan_id: bigint
  +periode: date
  +gaji_pokok: decimal
  +tunjangan: decimal
  +total_potongan: decimal
  +gaji_bersih: decimal
  +status: string
  +tanggal_bayar: date
  --
  +karyawan(): Karyawan
  +details(): PenggajianDetail[]
  +hitungGajiBersih(): decimal
  +generateSlipGaji(): PDF
}

class PenggajianDetail {
  +id: bigint
  +penggajian_id: bigint
  +potongan_id: bigint
  +nama_potongan: string
  +nilai_potongan: decimal
  --
  +penggajian(): Penggajian
  +potongan(): Potongan
}

User "1" -- "1" Karyawan
Jabatan "1" -- "0..*" Karyawan
Karyawan "1" -- "0..*" Penggajian
Penggajian "1" -- "1..*" PenggajianDetail
Potongan "1" -- "0..*" PenggajianDetail
@enduml
```

## 5. Deployment Diagram

```plantuml
@startuml Deployment_SistemPenggajian
node "Client Device" {
  [Web Browser]
}

node "Web Server" {
  [Nginx / Apache]
  [PHP-FPM 8.3]
  [Laravel 12 Application]
  [Tailwind CSS Assets]
}

node "Database Server" {
  database "MySQL" {
    [users, jabatans, karyawans]
    [potongans, penggajians, penggajian_detail]
  }
}

node "Mail Server" {
  [SMTP Service]
}

[Web Browser] --> [Nginx / Apache] : HTTPS
[Nginx / Apache] --> [PHP-FPM 8.3]
[PHP-FPM 8.3] --> [Laravel 12 Application]
[Laravel 12 Application] --> [MySQL] : Eloquent ORM / TCP 3306
[Laravel 12 Application] --> [SMTP Service] : Verifikasi Email / Reset Password
@enduml
```

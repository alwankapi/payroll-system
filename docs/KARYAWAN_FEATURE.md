# Fitur Karyawan

## Overview
Sistem Payroll sekarang memiliki 2 role:
1. **Admin** - Full access ke semua fitur
2. **Karyawan** - Akses terbatas untuk melihat data pribadi dan gaji

## Login Credentials

### Admin
- Email: `admin@payroll.test`
- Password: `password`

### Karyawan
- Email: `karyawan1@payroll.test` sampai `karyawan25@payroll.test`
- Password: `password`

## Fitur untuk Role Karyawan

### 1. Dashboard Karyawan
- URL: `/karyawan/dashboard`
- Menampilkan:
  - Welcome card dengan info karyawan
  - Statistik gaji bulan ini
  - Gaji terakhir
  - Quick links ke profil, riwayat gaji, ubah password

### 2. Profil Saya
- URL: `/karyawan/profil`
- Fitur:
  - Lihat profil lengkap (nama, NIP, email, jabatan, dll)
  - Edit data kontak (no telepon, alamat)
  - Upload foto profil (opsional)
  - Data sistem (nama, NIP, email, jabatan) TIDAK bisa diubah

### 3. Riwayat Gaji
- URL: `/karyawan/riwayat-gaji`
- Fitur:
  - Lihat semua riwayat penggajian
  - Filter berdasarkan bulan, tahun, status
  - Pagination
  - Detail slip gaji per periode
  - Download PDF slip gaji (untuk status final/dibayar)

### 4. Ubah Password
- URL: `/karyawan/password`
- Fitur:
  - Input password lama
  - Input password baru (min 8 karakter)
  - Konfirmasi password baru
  - Tips keamanan password

## Middleware & Security

### EnsureUserIsKaryawan
- File: `app/Http/Middleware/EnsureUserIsKaryawan.php`
- Memastikan hanya user dengan role 'karyawan' yang bisa akses routes `/karyawan/*`
- User admin akan di-redirect ke dashboard admin

### Route Protection
Semua route karyawan protected dengan:
```php
Route::middleware(['auth', 'verified', 'karyawan'])->prefix('karyawan')->name('karyawan.')->group(...)
```

## Controllers

### 1. Karyawan\DashboardController
- Method: `index()`
- Menampilkan dashboard dengan statistik gaji

### 2. Karyawan\ProfilController
- Method: `show()` - Lihat profil
- Method: `edit()` - Form edit profil
- Method: `update()` - Update profil (validasi via UpdateProfilRequest)
- Method: `editPassword()` - Form ubah password
- Method: `updatePassword()` - Update password (validasi via UpdatePasswordRequest)

### 3. Karyawan\RiwayatGajiController
- Method: `index()` - List riwayat gaji dengan filter & pagination
- Method: `show()` - Detail slip gaji
- Method: `download()` - Download PDF slip gaji (hanya untuk status final/dibayar)

## Views

### Layout
- Sidebar dinamis berdasarkan role (admin/karyawan)
- Menu karyawan: Dashboard, Profil Saya, Riwayat Gaji, Ubah Password

### Karyawan Views
- `resources/views/karyawan/dashboard.blade.php`
- `resources/views/karyawan/profil/show.blade.php`
- `resources/views/karyawan/profil/edit.blade.php`
- `resources/views/karyawan/profil/password.blade.php`
- `resources/views/karyawan/riwayat-gaji/index.blade.php`
- `resources/views/karyawan/riwayat-gaji/show.blade.php`
- `resources/views/karyawan/riwayat-gaji/pdf.blade.php`

## Form Validation

### UpdateProfilRequest
- `no_telepon`: required, max 15 karakter
- `alamat`: required
- `foto`: optional, image (jpeg/png/jpg), max 2MB

### UpdatePasswordRequest
- `current_password`: required, harus match dengan password lama
- `password`: required, min 8 karakter, confirmed

## Testing Manual

### Test Login Karyawan
1. Login dengan `karyawan1@payroll.test` / `password`
2. Seharusnya redirect ke `/karyawan/dashboard`
3. Cek sidebar hanya menampilkan menu karyawan

### Test Profil
1. Klik "Profil Saya" di sidebar
2. Cek data ditampilkan dengan benar
3. Klik "Edit Profil"
4. Ubah no telepon atau alamat
5. Submit dan cek berhasil update

### Test Riwayat Gaji
1. Klik "Riwayat Gaji" di sidebar
2. Cek list penggajian muncul
3. Klik "Detail" pada salah satu periode
4. Jika status = final/dibayar, tombol "Download PDF" muncul
5. Klik download dan cek PDF generate dengan benar

### Test Ubah Password
1. Klik "Ubah Password" di sidebar
2. Input password lama salah → error
3. Input password baru < 8 karakter → error
4. Input password baru tidak match konfirmasi → error
5. Input semua benar → berhasil update

## Catatan Penting

1. **Role Based Access**: Karyawan TIDAK bisa akses menu admin
2. **Data Restriction**: Karyawan hanya bisa lihat data milik sendiri
3. **Read Only**: Banyak data (nama, NIP, jabatan, gaji) READ ONLY untuk karyawan
4. **PDF Download**: Hanya tersedia untuk penggajian dengan status 'final' atau 'dibayar'

## Database

Seeder sudah include:
- 1 user admin
- 25 user karyawan (karyawan1@payroll.test s/d karyawan25@payroll.test)

Semua password default: `password`

# 💼 Sistem Penggajian Karyawan

> Aplikasi web untuk mengelola penggajian karyawan dengan perhitungan otomatis, workflow approval, dan laporan lengkap.

![Laravel](https://img.shields.io/badge/Laravel-13.x-red?logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.3-blue?logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.0-orange?logo=mysql)
![Tailwind CSS](https://img.shields.io/badge/Tailwind-3.x-cyan?logo=tailwindcss)

---

## ✨ Fitur Utama

- ✅ **Manajemen Karyawan** - Data karyawan lengkap dengan jabatan dan status
- ✅ **Perhitungan Gaji Otomatis** - Formula: Gaji Bersih = Gaji Pokok + Tunjangan - Potongan
- ✅ **Potongan Fleksibel** - Support nominal tetap & persentase dari gaji pokok
- ✅ **Generate Penggajian Massal** - Proses penggajian untuk semua karyawan dalam 1 klik
- ✅ **Workflow Approval** - 6 status: Draft → Diproses → Disetujui → Dibayar (+ Ditolak, Dibatalkan)
- ✅ **Slip Gaji PDF** - Generate dan download slip gaji profesional
- ✅ **Laporan Lengkap** - Export ke Excel dan PDF
- ✅ **Role-Based Access** - Admin (full access) & Karyawan (data sendiri only)
- ✅ **Dashboard Interaktif** - Statistik dan chart penggajian
- ✅ **Responsive Design** - Mobile-friendly dengan Tailwind CSS

---

## 🚀 Quick Start

### Prasyarat
- PHP >= 8.3
- Composer
- MySQL >= 8.0
- Node.js & NPM
- Git

### Instalasi

```bash
# 1. Clone repository
git clone https://github.com/alwankapi/payroll-system.git
cd payroll-system

# 2. Install dependencies
composer install
npm install

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Konfigurasi database di .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=sistem_penggajian
# DB_USERNAME=root
# DB_PASSWORD=your_password

# 5. Buat database
mysql -u root -p -e "CREATE DATABASE sistem_penggajian;"

# 6. Migrate & Seed database
php artisan migrate --seed

# 7. Build assets
npm run build

# 8. Jalankan aplikasi
php artisan serve
```

Aplikasi akan berjalan di **http://127.0.0.1:8000**

### Login Credentials

**Admin:**
- Email: `admin@payroll.test`
- Password: `password`

**Karyawan (contoh):**
- Email: `karyawan1@payroll.test` s/d `karyawan25@payroll.test`
- Password: `password`

---

## 📚 Dokumentasi

- **[Demo Guide](docs/DEMO_GUIDE.md)** - Panduan lengkap untuk demo ke klien
- **[Bug Fixes](docs/BUG_FIXES.md)** - Daftar bug yang telah diperbaiki
- **[Production Audit](docs/PRODUCTION_AUDIT.md)** - Audit pre-production
- **[Test Documentation](tests/README.md)** - Dokumentasi testing

---

## 🏗️ Tech Stack

### Backend
- **Laravel 13** - PHP Framework
- **MySQL** - Database
- **Laravel Breeze** - Authentication

### Frontend
- **Blade Templates** - Server-side rendering
- **Tailwind CSS** - Utility-first CSS
- **Alpine.js** - JavaScript interactivity

### Libraries
- **DomPDF** - PDF generation
- **Maatwebsite Excel** - Excel export
- **Chart.js** - Data visualization (via CDN)

---

## 📊 Database Schema

### Tables
- `users` - User accounts (admin & karyawan)
- `jabatans` - Master data jabatan
- `karyawans` - Data karyawan
- `potongans` - Master data potongan
- `penggajians` - Data penggajian
- `penggajian_details` - Detail potongan per penggajian (snapshot)

### Key Relationships
- User → Karyawan (1:1)
- Karyawan → Jabatan (N:1)
- Penggajian → Karyawan (N:1)
- Penggajian → PenggajianDetail (1:N)

---

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature

# Run with coverage
php artisan test --coverage
```

**Test Coverage:**
- ✅ Unit Tests: PenggajianService, Salary Calculation, Potongan Calculation
- ✅ Feature Tests: CRUD operations, Authorization, PDF Export
- ✅ Integration Tests: End-to-end workflows

---

## 🔧 Development

```bash
# Run development server with hot reload
npm run dev

# In another terminal
php artisan serve

# Run queue worker (if using queues)
php artisan queue:work

# Clear all caches
php artisan optimize:clear
```

---

## 📝 Business Rules

### BR-03: Formula Gaji
```
Gaji Bersih = Gaji Pokok + Tunjangan Jabatan − Total Potongan
```

### BR-04: Jenis Potongan
- **Nominal:** Nilai tetap (contoh: Rp 50.000)
- **Persentase:** Dihitung dari gaji pokok (contoh: 5% × Gaji Pokok)

### BR-05: Validasi Duplikasi
Satu karyawan hanya boleh memiliki satu penggajian per periode

### BR-06: Status Terkunci
Penggajian dengan status `diproses`, `disetujui`, atau `dibayar` tidak dapat diubah/dihapus

### BR-08: Karyawan Aktif
Hanya karyawan dengan status `aktif` yang diikutkan dalam generate penggajian massal

### BR-10: Snapshot Data
Data gaji pokok, tunjangan, dan potongan di-snapshot saat penggajian dibuat. Perubahan master data tidak mempengaruhi penggajian yang sudah ada.

---

## 🔐 Security

- ✅ Authentication dengan Laravel Breeze
- ✅ Authorization dengan Policy & Middleware
- ✅ CSRF Protection
- ✅ SQL Injection Prevention (Eloquent ORM)
- ✅ XSS Protection (Blade escaping)
- ✅ Password Hashing (bcrypt)
- ✅ Role-based Access Control

---

## 🐛 Troubleshooting

### Error: "SQLSTATE[HY000] [2002] Connection refused"
```bash
# Pastikan MySQL berjalan
sudo systemctl start mysql

# Cek kredensial di .env
```

### Error: "Target class [Controller] does not exist"
```bash
php artisan clear-compiled
composer dump-autoload
```

### PDF tidak ter-generate
```bash
# Clear cache
php artisan view:clear
php artisan config:clear
```

### Assets tidak muncul
```bash
npm run build
php artisan storage:link
```

---

## 📈 Performance Tips

```bash
# Production optimization
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Database optimization
php artisan db:show
# Add indexes jika perlu
```

---

## 🤝 Contributing

1. Fork repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

---

## 📄 License

This project is licensed under the MIT License.

---

## 👨‍💻 Developer

**Repository:** https://github.com/alwankapi/payroll-system

---

## 📞 Support

Jika menemukan bug atau ada pertanyaan, silakan buat issue di GitHub repository.

---

**Built with ❤️ using Laravel**

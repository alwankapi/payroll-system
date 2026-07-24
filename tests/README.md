# Test Suite Documentation - Sistem Penggajian Laravel 12

## Overview

Test suite lengkap untuk aplikasi Sistem Penggajian dengan coverage komprehensif untuk business logic, API endpoints, authorization, dan integrasi.

**Total: 8 Test Files | ~1,600+ Lines | 80+ Test Cases**

---

## 📂 Test Structure

### Unit Tests (3 files - 570 lines)

#### 1. **PenggajianServiceTest.php** (220 lines)
Tests untuk core business logic penggajian service
- ✅ Perhitungan gaji bersih
- ✅ Kalkulasi potongan (nominal & persentase)
- ✅ Generate bulk penggajian
- ✅ Validasi duplikasi periode
- ✅ Status workflow (draft → final → dibayar)
- ✅ Edge cases dan error handling

#### 2. **SalaryCalculationTest.php** (155 lines)
Tests untuk kalkulasi komponen gaji
- ✅ Gaji pokok + tunjangan
- ✅ Total gaji kotor
- ✅ Gaji bersih setelah potongan
- ✅ Kombinasi multiple potongan
- ✅ Validasi nilai minimum/maksimum

#### 3. **PotonganCalculationTest.php** (195 lines)
Tests untuk sistem potongan
- ✅ Potongan jenis nominal
- ✅ Potongan jenis persentase
- ✅ Multiple potongan aktif
- ✅ Filter potongan by status
- ✅ Validasi range persentase (0-100%)

---

### Feature Tests (5 files - 1,043 lines)

#### 4. **JabatanTest.php** (213 lines)
Tests CRUD dan fitur modul Jabatan
- ✅ Authentication & authorization
- ✅ Index dengan pagination, search, sort
- ✅ Create dengan validasi
- ✅ Read/show detail
- ✅ Update jabatan
- ✅ Delete jabatan
- ✅ Validasi gaji_pokok & tunjangan_jabatan

#### 5. **KaryawanTest.php** (215 lines)
Tests CRUD dan fitur modul Karyawan
- ✅ CRUD operations
- ✅ Validasi NIK unique
- ✅ Validasi email format
- ✅ Filter by status (aktif/nonaktif)
- ✅ Filter by jabatan
- ✅ Search by NIK/nama
- ✅ Validasi tanggal_masuk (tidak boleh future)

#### 6. **PotonganTest.php** (185 lines)
Tests CRUD dan fitur modul Potongan
- ✅ Create potongan nominal & persentase
- ✅ Validasi jenis_potongan
- ✅ Validasi nilai > 0
- ✅ Validasi persentase ≤ 100
- ✅ Filter by jenis & status
- ✅ Search potongan

#### 7. **PenggajianTest.php** (210 lines)
Tests CRUD dan workflow penggajian
- ✅ CRUD penggajian
- ✅ Validasi duplikasi periode
- ✅ Edit hanya untuk status draft
- ✅ Delete hanya untuk status draft
- ✅ Filter by status, periode, karyawan
- ✅ Generate bulk penggajian
- ✅ Update status (draft→final→dibayar)
- ✅ Business rules enforcement

#### 8. **AuthorizationPdfExportTest.php** (220 lines)
Tests authorization, PDF generation, dan export
- ✅ Guest redirection to login
- ✅ Authenticated user access
- ✅ Generate slip gaji PDF
- ✅ Preview slip gaji
- ✅ Validasi konten slip gaji
- ✅ Prevent PDF untuk status draft
- ✅ Generate laporan PDF
- ✅ Export to Excel
- ✅ Export format validation
- ✅ Complete workflow integration test

---

## 🚀 Running Tests

### Run All Tests
```bash
php artisan test
```

### Run Specific Test Suite
```bash
# Unit tests only
php artisan test --testsuite=Unit

# Feature tests only
php artisan test --testsuite=Feature
```

### Run Specific Test File
```bash
php artisan test tests/Unit/PenggajianServiceTest.php
php artisan test tests/Feature/JabatanTest.php
```

### Run with Coverage
```bash
php artisan test --coverage
```

### Run with Verbose Output
```bash
php artisan test --verbose
```

---

## 📋 Test Coverage Areas

### ✅ Business Logic
- Salary calculation engine
- Potongan calculation (nominal & percentage)
- Status workflow validation
- Duplicate prevention
- Data integrity rules

### ✅ API Endpoints
- All CRUD operations
- Filtering & searching
- Sorting & pagination
- Bulk operations
- Status updates

### ✅ Authorization
- Guest access control
- Authenticated user permissions
- Resource ownership validation

### ✅ Data Validation
- Required fields
- Unique constraints
- Email format
- Date validation
- Numeric ranges
- Enum values

### ✅ PDF & Export
- Slip gaji generation
- PDF content validation
- Excel export
- File format verification

### ✅ Integration
- Complete workflows
- Multi-step operations
- Cross-module interactions

---

## 🔧 Test Environment Setup

### Prerequisites
```bash
# Install dependencies
composer install

# Setup test database
cp .env.example .env.testing

# Configure .env.testing
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
```

### Database Setup
Tests menggunakan `RefreshDatabase` trait untuk:
- Fresh database per test
- Automatic migrations
- Isolated test data
- No pollution between tests

---

## 📊 Test Metrics

- **Total Test Files**: 8
- **Total Lines**: ~1,613
- **Unit Tests**: 3 files (30+ cases)
- **Feature Tests**: 5 files (50+ cases)
- **Expected Coverage**: ≥80%
- **Modules Covered**: 100%

---

## 🎯 Test Quality Standards

### All Tests Follow:
- ✅ Descriptive test names
- ✅ Arrange-Act-Assert pattern
- ✅ Single responsibility per test
- ✅ Proper setup/teardown
- ✅ Mock/fake when appropriate
- ✅ Edge case coverage
- ✅ Error scenario testing

---

## 📝 Notes

### Known Issues
- SQLite driver diperlukan untuk in-memory testing
- Install php-sqlite3 jika tests gagal dengan "could not find driver"

### Installation (Ubuntu/Debian)
```bash
sudo apt-get install php-sqlite3
sudo systemctl restart apache2  # or your web server
```

### Future Improvements
- [ ] Add performance tests
- [ ] Add browser tests (Dusk)
- [ ] Add API documentation tests
- [ ] Increase coverage to 90%+
- [ ] Add mutation testing

---

## 👨‍💻 Maintained By

Sistem Penggajian Development Team
Created: 2026-01-24
Laravel Version: 12.x
PHP Version: 8.2+

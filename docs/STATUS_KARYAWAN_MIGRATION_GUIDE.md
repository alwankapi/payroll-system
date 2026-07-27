# 🔄 Status Karyawan Migration Guide
**From: Aktif/Nonaktif → To: Tetap/Kontrak/Magang**

## 📋 Overview

Dokumen ini menjelaskan proses migrasi status karyawan dari sistem binary (Aktif/Nonaktif) menjadi sistem tiga kategori yang lebih profesional (Tetap/Kontrak/Magang) sesuai standar industri.

## ✅ What Has Been Completed

### Phase 1: Core Logic Layer ✅
- [x] **Migration File**: `2026_07_27_172229_update_karyawan_status_to_employment_type.php`
- [x] **Model Karyawan**: Added helper methods (`statusLabel()`, `statusBadgeClass()`, `scopeByStatus()`)
- [x] **Form Requests**: 
  - `StoreKaryawanRequest`: Updated validation rules
  - `UpdateKaryawanRequest`: Updated validation rules
- [x] **Controllers**:
  - `KaryawanController`: Updated queries `whereIn(['tetap', 'kontrak', 'magang'])`
  - `JabatanController`: Updated queries
  - `PenggajianController`: Updated queries  
  - `KehadiranController`: Updated queries
- [x] **Services**:
  - `PenggajianService`: Updated bulk generation logic
- [x] **Views (Partial)**:
  - `karyawan/create.blade.php`: ✅ Form updated

### Phase 2: Remaining Views 🔧
Use the automated script to fix:
- `karyawan/edit.blade.php`
- `karyawan/index.blade.php`
- `karyawan/show.blade.php`
- `karyawan/profil/show.blade.php`
- `jabatan/show.blade.php`
- `penggajian/generate-bulk.blade.php`

### Phase 3: Data & Tests 🔧
- `database/seeders/KaryawanSeeder.php` - Needs manual update
- `tests/Feature/KaryawanTest.php` - Will be auto-fixed
- `tests/Unit/PenggajianServiceTest.php` - Will be auto-fixed

## 🚀 Execution Steps

### Step 1: Run the Migration

```bash
php artisan migrate
```

This will update the database column type to enum with new values.

### Step 2: Run the Auto-Fix Script

```bash
chmod +x fix-status-karyawan.sh
./fix-status-karyawan.sh
```

The script will:
- ✅ Create automatic backup
- ✅ Update all view files
- ✅ Update test files
- ✅ Use Model helper methods for badges
- ⚠️ Flag KaryawanSeeder for manual review

### Step 3: Manual Update - KaryawanSeeder

Edit `database/seeders/KaryawanSeeder.php` and vary the status values:

```php
// Before (all 'aktif')
'status_karyawan' => 'aktif',

// After (varied)
'status_karyawan' => $faker->randomElement(['tetap', 'kontrak', 'magang']),
```

### Step 4: Reseed Database

```bash
php artisan migrate:fresh --seed
```

### Step 5: Run Tests

```bash
php artisan test
```

Expected results:
- All tests should pass
- No references to 'aktif' or 'nonaktif' in test output

## 📊 Status Mapping

| Old Status | New Status | Badge Color | Description |
|-----------|-----------|-------------|-------------|
| `aktif` | `tetap` | 🟢 Green | Karyawan Tetap (Permanent Employee) |
| `aktif` | `kontrak` | 🟡 Yellow | Karyawan Kontrak (Contract Employee) |
| `aktif` | `magang` | 🔵 Blue | Karyawan Magang (Intern) |
| `nonaktif` | *(removed)* | - | No longer used |

## 🎨 Badge Classes

The Model provides consistent badge styling:

```php
// In Model
public function statusBadgeClass(): string
{
    return match($this->status_karyawan) {
        'tetap' => 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
        'kontrak' => 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
        'magang' => 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
        default => 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300',
    };
}
```

## 🔍 Verification Checklist

After completing all steps, verify:

### Database
- [ ] Migration ran successfully
- [ ] `karyawans` table has enum(`tetap`, `kontrak`, `magang`)
- [ ] No old 'aktif' or 'nonaktif' values in database

### Forms
- [ ] Create form shows: Tetap, Kontrak, Magang options
- [ ] Edit form shows: Tetap, Kontrak, Magang options
- [ ] Default value is 'tetap'

### Display
- [ ] Index page badges show correct colors
- [ ] Show page badges show correct colors
- [ ] Filter dropdown has all three options
- [ ] Profil karyawan shows correct status

### Logic
- [ ] Penggajian bulk generation works for all status
- [ ] Kehadiran filter works for all status
- [ ] Jabatan can delete only if no karyawan with any status
- [ ] Controllers query using `whereIn(['tetap', 'kontrak', 'magang'])`

### Tests
- [ ] All Feature tests pass
- [ ] All Unit tests pass
- [ ] No test failures related to status

## 🐛 Troubleshooting

### Issue: Migration fails with "enum values don't match"

**Solution:**
```bash
# Check existing data
php artisan tinker
>>> DB::table('karyawans')->distinct()->pluck('status_karyawan');

# If old values exist, update them first
>>> DB::table('karyawans')->where('status_karyawan', 'aktif')->update(['status_karyawan' => 'tetap']);
>>> DB::table('karyawans')->where('status_karyawan', 'nonaktif')->update(['status_karyawan' => 'magang']);

# Then run migration
php artisan migrate
```

### Issue: Tests fail with "aktif is not a valid value"

**Solution**: Run the auto-fix script again - it updates test files.

### Issue: Badges show old styling

**Solution**: Clear view cache
```bash
php artisan view:clear
php artisan config:clear
php artisan cache:clear
```

## 📚 Business Rules Affected

### BR-08: Karyawan Status Validation
- **Old**: Only 'aktif' karyawan can be processed for payroll
- **New**: All three statuses ('tetap', 'kontrak', 'magang') can be processed

### BR-09: Jabatan Deletion
- **Old**: Cannot delete if any 'aktif' karyawan exists
- **New**: Cannot delete if any karyawan with status 'tetap', 'kontrak', or 'magang' exists

### Query Filters
All `where('status_karyawan', 'aktif')` changed to:
```php
whereIn('status_karyawan', ['tetap', 'kontrak', 'magang'])
```

## 🎯 Benefits

1. **More Professional**: Aligns with HR industry standards
2. **Better Classification**: Clear distinction between employment types
3. **Flexible Policies**: Different benefits per status possible
4. **Reporting**: Better analytics on workforce composition
5. **Compliance**: Matches labor law classifications

## 📝 Notes

- Old 'nonaktif' concept is removed - use deletion or archiving instead
- All three statuses are considered "active" employees
- Badge colors are consistent with UI/UX best practices
- Model helpers ensure DRY principle

## 🔗 Related Documentation

- Migration file: `database/migrations/2026_07_27_172229_update_karyawan_status_to_employment_type.php`
- Audit document: `docs/STATUS_KARYAWAN_AUDIT.md`
- Model changes: `app/Models/Karyawan.php`

---

**Last Updated**: 2026-07-28  
**Migration Status**: ✅ Core Logic Complete | 🔧 Views Pending Script Execution

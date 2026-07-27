# 🧪 Testing Guide - PDF Laporan Penggajian

## 📋 Quick Start

**Objective:** Memastikan Export PDF Laporan Penggajian bekerja tanpa error dengan layout yang rapi.

**Duration:** ~15 menit

---

## ✅ Pre-Testing Setup

### 1. Ensure Data Exists

```bash
# Seed database jika belum ada data
php artisan db:seed --class=DatabaseSeeder

# Or specific seeders
php artisan db:seed --class=JabatanSeeder
php artisan db:seed --class=KaryawanSeeder
php artisan db:seed --class=PenggajianSeeder
```

### 2. Clear Cache

```bash
php artisan optimize:clear
```

### 3. Start Server

```bash
php artisan serve
```

### 4. Login as Admin

```
URL: http://localhost:8000/login
Email: admin@payroll.com
Password: password
```

---

## 🎯 Test Cases

### Test Case 1: Basic PDF Export ✅

**Priority:** CRITICAL

**Steps:**
1. Navigate to `/laporan`
2. Click button "Export PDF" (merah)
3. Observe PDF download

**Expected Result:**
- ✅ File downloads dengan nama format: `laporan-penggajian-YYYYMMDD-HHMMSS.pdf`
- ✅ File size reasonable (~200-500KB tergantung jumlah data)
- ✅ Dapat dibuka tanpa error

**Actual Result:**
- [ ] PASS
- [ ] FAIL - Note: _______________

---

### Test Case 2: PDF Content Verification ✅

**Priority:** CRITICAL

**Steps:**
1. Open downloaded PDF
2. Verify all sections present

**Expected Result:**

**Header Section:**
- ✅ Company name: "PT. Sistem Penggajian Indonesia"
- ✅ Address & contact info
- ✅ Title: "LAPORAN PENGGAJIAN"

**Info Section:**
- ✅ Periode Bulan: "Semua"
- ✅ Tahun: "Semua"
- ✅ Filter Jabatan: "Semua"
- ✅ Status: "Semua"
- ✅ Tanggal Cetak: [Current date & time] WIB

**Summary Section:**
- ✅ Total Gaji Pokok: Rp [amount]
- ✅ Total Tunjangan: Rp [amount]
- ✅ Total Potongan: Rp [amount]
- ✅ Total Gaji Bersih: Rp [amount] (highlighted)
- ✅ Jumlah Karyawan: [X] Orang
- ✅ Total Transaksi: [Y] Data

**Table Section:**
- ✅ Table header visible dengan 9 columns
- ✅ Data rows complete
- ✅ Numbers right-aligned
- ✅ Currency format: Rp X.XXX (dengan pemisah titik)
- ✅ Date format: M Y (contoh: Jul 2026)

**Footer Section:**
- ✅ Company name
- ✅ "Dokumen ini digenerate..."
- ✅ Copyright © 2026

**Actual Result:**
- [ ] PASS
- [ ] FAIL - Missing: _______________

---

### Test Case 3: Filter by Bulan ✅

**Priority:** HIGH

**Steps:**
1. Go to `/laporan`
2. Select "July" dari dropdown Bulan
3. Click "Filter"
4. Verify data filtered
5. Click "Export PDF"
6. Open PDF

**Expected Result:**
- ✅ Filter Info shows: Bulan = "July"
- ✅ Only July data in table
- ✅ Summary calculations match filtered data
- ✅ PDF downloads successfully

**Actual Result:**
- [ ] PASS
- [ ] FAIL - Note: _______________

---

### Test Case 4: Filter by Multiple Criteria ✅

**Priority:** HIGH

**Steps:**
1. Apply filters:
   - Bulan: July
   - Tahun: 2026
   - Status: Dibayar
2. Click "Filter"
3. Click "Export PDF"

**Expected Result:**
- ✅ All filters shown in PDF Info section
- ✅ Data matches all filters
- ✅ If no data: "Tidak ada data penggajian sesuai dengan filter..."

**Actual Result:**
- [ ] PASS
- [ ] FAIL - Note: _______________

---

### Test Case 5: Portrait Layout Check ✅

**Priority:** HIGH

**Steps:**
1. Export PDF
2. Open in PDF viewer
3. Check "Document Properties" or page orientation

**Expected Result:**
- ✅ Paper size: A4 (210 x 297 mm)
- ✅ Orientation: Portrait (vertical)
- ✅ All content fits within margins
- ✅ No horizontal scroll needed
- ✅ No content cut off

**Actual Result:**
- [ ] PASS
- [ ] FAIL - Content cut off at: _______________

---

### Test Case 6: Large Dataset (50+ records) ⚡

**Priority:** HIGH

**Steps:**
```bash
# Generate more penggajian if needed
php artisan tinker
>>> App\Models\Penggajian::factory()->count(50)->create();
>>> exit
```

1. Go to `/laporan`
2. No filter (show all)
3. Click "Export PDF"
4. Measure time taken

**Expected Result:**
- ✅ PDF generates in < 10 seconds
- ✅ All 50+ records in PDF
- ✅ Multi-page if needed
- ✅ No timeout error
- ✅ File size < 2MB

**Actual Result:**
- [ ] PASS - Time: _____ seconds
- [ ] FAIL - Error: _______________

---

### Test Case 7: Empty Data State ✅

**Priority:** MEDIUM

**Steps:**
1. Apply filter yang tidak match data apapun
   - Example: Tahun = 2020 (no data)
2. Click "Export PDF"

**Expected Result:**
- ✅ PDF still generates
- ✅ Summary shows all zeros
- ✅ Table shows: "Tidak ada data penggajian sesuai dengan filter yang dipilih"
- ✅ No error

**Actual Result:**
- [ ] PASS
- [ ] FAIL - Note: _______________

---

### Test Case 8: Potongan Display ✅

**Priority:** MEDIUM

**Steps:**
1. Find penggajian with potongan
2. Export PDF
3. Check potongan display in table

**Expected Result:**
- ✅ Each potongan shown in list
- ✅ Format: [Nama] (jenis) - Rp [nilai]
- ✅ Total potongan at bottom
- ✅ If no potongan: "Tidak ada potongan"

**Actual Result:**
- [ ] PASS
- [ ] FAIL - Note: _______________

---

### Test Case 9: Print Test 🖨️

**Priority:** MEDIUM

**Steps:**
1. Open PDF
2. File → Print (or Ctrl+P)
3. Print Preview

**Expected Result:**
- ✅ Preview looks good
- ✅ No content overflow
- ✅ Margins appropriate
- ✅ Readable when printed
- ✅ Header/footer on all pages

**Actual Result:**
- [ ] PASS
- [ ] FAIL - Issue: _______________

---

### Test Case 10: Currency Format ✅

**Priority:** LOW

**Steps:**
1. Export PDF
2. Check all Rupiah values

**Expected Result:**
- ✅ Format: Rp 5.000.000 (with dots)
- ✅ No decimals shown
- ✅ Right-aligned in table
- ✅ Consistent throughout

**Actual Result:**
- [ ] PASS
- [ ] FAIL - Wrong format: _______________

---

## 🔍 Edge Cases

### Edge Case 1: Special Characters in Name

**Setup:**
```sql
-- Create karyawan with special chars
UPDATE karyawans SET nama_lengkap = 'O\'Brien, Jr.' WHERE id = 1;
```

**Test:**
- Export PDF
- Verify name displays correctly (no encoding issues)

**Result:** 
- [ ] PASS
- [ ] FAIL

---

### Edge Case 2: Very Long Name (50+ chars)

**Setup:**
```sql
UPDATE karyawans SET nama_lengkap = 'Muhammad Abdullah Bin Sulaiman Bin Abdul Rahman Al-Farisi' WHERE id = 1;
```

**Test:**
- Export PDF
- Check if name truncated or wrapped properly

**Result:**
- [ ] PASS
- [ ] FAIL

---

### Edge Case 3: Many Potongan (10+)

**Test:**
- Create penggajian with 10 different potongan
- Export PDF
- Check potongan column

**Expected:**
- Small font but readable
- Or shows first few + "..."

**Result:**
- [ ] PASS
- [ ] FAIL

---

## ⚡ Performance Tests

### Performance Test 1: Query Count

**Tool:** Laravel Debugbar or Telescope

**Steps:**
1. Enable query logging
2. Export PDF
3. Check number of queries

**Expected:**
- ✅ Single query untuk penggajians dengan eager loading
- ✅ No N+1 problem

**Actual Queries:** _____

**Result:**
- [ ] PASS (<5 queries)
- [ ] FAIL (N+1 detected)

---

### Performance Test 2: Memory Usage

**Steps:**
```bash
# Check before export
free -h

# Export PDF for 100 records

# Check after
free -h
```

**Expected:**
- ✅ Memory spike < 100MB
- ✅ Memory releases after generation

**Result:**
- [ ] PASS
- [ ] FAIL - Peak memory: _______________

---

### Performance Test 3: Generation Time

**Steps:**
1. Use browser DevTools Network tab
2. Click Export PDF
3. Measure time from request to download

**Benchmarks:**
- 10 records: < 2 seconds
- 50 records: < 5 seconds
- 100 records: < 10 seconds

**Actual Times:**
- 10 records: _____ sec
- 50 records: _____ sec
- 100 records: _____ sec

**Result:**
- [ ] PASS (within benchmarks)
- [ ] FAIL (too slow)

---

## 🚨 Error Scenarios

### Error Scenario 1: No Data Seeded

**Steps:**
1. Fresh database (no data)
2. Navigate to `/laporan`
3. Click Export PDF

**Expected:**
- ✅ PDF generates
- ✅ Shows "Tidak ada data"
- ✅ Summary all zeros
- ✅ No 500 error

**Result:**
- [ ] PASS
- [ ] FAIL

---

### Error Scenario 2: Broken Relationship

**Steps:**
1. Manually delete a jabatan used by karyawan (breaks FK temporarily)
2. Try export PDF

**Expected:**
- ✅ Error caught gracefully
- ✅ User sees error message
- ✅ Or data skipped with note

**Result:**
- [ ] PASS
- [ ] FAIL

---

## 📊 Test Summary Template

### Test Session Info

**Date:** _______________
**Tester:** _______________
**Environment:** _______________
**PHP Version:** _______________
**Browser:** _______________

### Results

| Test Case | Priority | Status | Notes |
|-----------|----------|--------|-------|
| TC1: Basic Export | CRITICAL | ☐ PASS ☐ FAIL | |
| TC2: Content Verify | CRITICAL | ☐ PASS ☐ FAIL | |
| TC3: Filter Bulan | HIGH | ☐ PASS ☐ FAIL | |
| TC4: Multiple Filters | HIGH | ☐ PASS ☐ FAIL | |
| TC5: Portrait Layout | HIGH | ☐ PASS ☐ FAIL | |
| TC6: Large Dataset | HIGH | ☐ PASS ☐ FAIL | |
| TC7: Empty State | MEDIUM | ☐ PASS ☐ FAIL | |
| TC8: Potongan Display | MEDIUM | ☐ PASS ☐ FAIL | |
| TC9: Print Test | MEDIUM | ☐ PASS ☐ FAIL | |
| TC10: Currency Format | LOW | ☐ PASS ☐ FAIL | |

**Edge Cases:**
- Edge Case 1: ☐ PASS ☐ FAIL
- Edge Case 2: ☐ PASS ☐ FAIL
- Edge Case 3: ☐ PASS ☐ FAIL

**Performance:**
- Query Count: ☐ PASS ☐ FAIL
- Memory Usage: ☐ PASS ☐ FAIL
- Generation Time: ☐ PASS ☐ FAIL

**Error Scenarios:**
- Error 1: ☐ PASS ☐ FAIL
- Error 2: ☐ PASS ☐ FAIL

### Overall Score

**Total Tests:** 10 + 3 + 3 + 2 = 18
**Passed:** _____
**Failed:** _____
**Pass Rate:** _____% 

### Decision

- [ ] ✅ **APPROVED** - Ready for production
- [ ] ⚠️ **CONDITIONAL** - Minor fixes needed
- [ ] ❌ **REJECTED** - Major issues, needs rework

### Critical Issues Found

1. ________________________________
2. ________________________________
3. ________________________________

### Recommendations

1. ________________________________
2. ________________________________
3. ________________________________

---

## 🔧 Quick Fixes

### If PDF is Blank

```bash
php artisan view:clear
php artisan config:clear
# Check storage/logs/laravel.log
```

### If Layout Broken

Check browser console for:
- CSS loading errors
- JavaScript errors
- Check PDF in different viewer

### If Slow Performance

```bash
# Enable query log
php artisan debugbar:enable

# Check N+1
# Should see: with(['karyawan.jabatan', 'details.potongan'])
```

---

## 📞 Support

**If tests fail:**
1. Check `storage/logs/laravel.log`
2. Clear all caches
3. Verify database connections
4. Check DomPDF package installed: `composer show barryvdh/laravel-dompdf`

**Contact:**
- Developer: [Your Name]
- Documentation: `docs/PDF_LAPORAN_FIXES.md`

---

**Last Updated:** 2026-07-27
**Version:** 1.0
**Status:** ✅ Ready for Testing

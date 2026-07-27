# 📋 Panduan Testing Manual - Modul Penggajian

## Overview

Dokumen ini berisi langkah-langkah testing manual untuk memastikan modul penggajian berfungsi dengan baik setelah bug fixes.

**Target Testing:**
- ✅ Form Create Penggajian
- ✅ Form Edit Penggajian
- ✅ Calculation & Preview
- ✅ Validation
- ✅ Data Persistence

---

## Persiapan Testing

### 1. Setup Environment
```bash
# Clear cache
php artisan cache:clear
php artisan view:clear
php artisan config:clear

# Ensure database is seeded
php artisan db:seed --class=JabatanSeeder
php artisan db:seed --class=KaryawanSeeder
php artisan db:seed --class=PotonganSeeder
php artisan db:seed --class=UserSeeder
```

### 2. Login Credentials
```
Admin:
Email: admin@payroll.com
Password: password

Karyawan (optional for testing):
Email: ahmad@payroll.com
Password: password
```

### 3. Browser Requirements
- Chrome 115+ / Firefox 116+ / Safari 16+
- JavaScript enabled
- Console terbuka untuk debugging (F12)

---

## Test Suite 1: CREATE PENGGAJIAN

### Test Case 1.1: Happy Path - Create dengan Potongan

**Objective:** Memastikan form create berfungsi normal dengan semua fitur

**Steps:**
1. Login sebagai Admin
2. Navigate ke **Penggajian** → **Tambah Penggajian**
3. URL: `http://localhost:8000/penggajian/create`

4. **Pilih Karyawan**
   - Select dropdown karyawan
   - Pilih: "Ahmad Fauzi - Staff Admin"
   - ✅ **Expected:** Gaji pokok dan tunjangan auto-fill

5. **Cek Auto-fill**
   - ✅ **Expected:** 
     - Gaji Pokok terisi (misal: 5000000)
     - Tunjangan terisi (misal: 1000000)
     - Preview box update otomatis

6. **Pilih Periode**
   - Input periode: Juli 2026 (2026-07)
   - ✅ **Expected:** Input accepted

7. **Pilih Potongan**
   - Check: BPJS Kesehatan (2%)
   - Check: Pajak Penghasilan (5%)
   - ✅ **Expected:** 
     - Preview "Total Potongan" update
     - Detail potongan muncul (BPJS: Rp X, Pajak: Rp Y)
     - Gaji Bersih recalculate

8. **Verify Preview Calculation**
   - ✅ **Expected Preview:**
     ```
     Gaji Pokok:     Rp 5,000,000
     Tunjangan:      Rp 1,000,000
     ─────────────────────────────
     Total Potongan: Rp 350,000
       • BPJS (2%):  Rp 100,000
       • Pajak (5%): Rp 250,000
     ═════════════════════════════
     Gaji Bersih:    Rp 5,650,000
     ```

9. **Set Status**
   - Pilih Status: Draft
   - ✅ **Expected:** Default "Draft" selected

10. **Submit Form**
    - Click "Simpan Penggajian"
    - ✅ **Expected:**
      - Success message: "Data penggajian berhasil dibuat"
      - Redirect ke `/penggajian`
      - Data muncul di tabel

11. **Verify Database**
    ```bash
    php artisan tinker
    >>> $p = \App\Models\Penggajian::latest()->first()
    >>> $p->gaji_pokok  # Should be 5000000
    >>> $p->tunjangan   # Should be 1000000
    >>> $p->total_potongan  # Should be 350000
    >>> $p->gaji_bersih  # Should be 5650000
    >>> $p->details  # Should have 2 records
    ```

**Result:** ✅ PASS / ❌ FAIL

**Notes:**
_____________________________________________

---

### Test Case 1.2: Create Tanpa Potongan

**Objective:** Memastikan form bisa submit tanpa potongan

**Steps:**
1. Navigate ke `/penggajian/create`
2. Pilih karyawan: "Budi Santoso - Manager"
3. Pilih periode: Agustus 2026
4. **JANGAN** check potongan apapun
5. Verify preview:
   - ✅ Total Potongan: Rp 0
   - ✅ Gaji Bersih = Gaji Pokok + Tunjangan
6. Submit form
7. ✅ **Expected:** Success, data tersimpan dengan total_potongan = 0

**Result:** ✅ PASS / ❌ FAIL

---

### Test Case 1.3: Validation - Required Fields

**Objective:** Memastikan validation bekerja

**Steps:**
1. Navigate ke `/penggajian/create`
2. **Submit form kosong** (tanpa isi apapun)
3. ✅ **Expected Errors:**
   - "Karyawan wajib dipilih"
   - "Periode wajib diisi"
   - "Gaji Pokok wajib diisi"
   - "Tunjangan wajib diisi"
   - "Status wajib dipilih"

**Result:** ✅ PASS / ❌ FAIL

---

### Test Case 1.4: Validation - Duplicate Prevention

**Objective:** Mencegah duplikat karyawan + periode

**Steps:**
1. Create penggajian: Karyawan A, Periode Juli 2026 → Success
2. Try create lagi: Karyawan A, Periode Juli 2026
3. ✅ **Expected:** 
   - Validation error
   - Message: "Data penggajian untuk karyawan dan periode ini sudah ada"

**Result:** ✅ PASS / ❌ FAIL

---

### Test Case 1.5: Dynamic Calculation - Persentase

**Objective:** Memastikan potongan persentase dihitung benar

**Steps:**
1. Create penggajian dengan:
   - Gaji Pokok: Rp 10,000,000
   - Tunjangan: Rp 2,000,000
   - Check BPJS (2%)
2. ✅ **Expected Preview:**
   - BPJS: Rp 200,000 (2% dari 10,000,000)
   - Total Potongan: Rp 200,000
   - Gaji Bersih: Rp 11,800,000

**Result:** ✅ PASS / ❌ FAIL

---

### Test Case 1.6: Dynamic Calculation - Nominal

**Objective:** Memastikan potongan nominal benar

**Steps:**
1. Assume ada potongan "Pinjaman Karyawan" Rp 500,000 (nominal)
2. Create penggajian:
   - Gaji Pokok: Rp 5,000,000
   - Check Pinjaman: Rp 500,000
3. ✅ **Expected:**
   - Total Potongan: Rp 500,000 (fixed, tidak terpengaruh gaji pokok)

**Result:** ✅ PASS / ❌ FAIL

---

## Test Suite 2: EDIT PENGGAJIAN

### Test Case 2.1: Edit Draft Penggajian

**Objective:** Memastikan edit form berfungsi untuk status draft

**Steps:**
1. Dari index penggajian, pilih data dengan status "Draft"
2. Click icon "Edit"
3. ✅ **Expected:**
   - Form loaded dengan data existing
   - Preview menampilkan perhitungan yang benar
   - Checkbox potongan sesuai dengan yang tersimpan

4. **Modify Data:**
   - Ubah periode ke bulan berbeda
   - Uncheck salah satu potongan
   - Check potongan lain

5. **Verify Preview Update:**
   - ✅ Preview recalculate sesuai perubahan

6. **Submit:**
   - Click "Update Penggajian"
   - ✅ **Expected:**
     - Success message
     - Redirect ke index
     - Data terupdate

**Result:** ✅ PASS / ❌ FAIL

---

### Test Case 2.2: Edit - Change Karyawan

**Objective:** Memastikan bisa ganti karyawan di edit

**Steps:**
1. Edit penggajian existing
2. Ganti karyawan ke karyawan lain
3. ✅ **Expected:**
   - Gaji pokok & tunjangan auto-update sesuai karyawan baru
   - Preview recalculate
4. Submit → Success

**Result:** ✅ PASS / ❌ FAIL

---

### Test Case 2.3: Edit - Locked Status (Final/Dibayar)

**Objective:** Memastikan status Final/Dibayar tidak bisa diedit

**Steps:**
1. Create penggajian dengan status "Final"
2. Try access edit page
3. ✅ **Expected:**
   - Redirect back dengan error message
   - Message: "Penggajian dengan status final tidak dapat diubah"

**Result:** ✅ PASS / ❌ FAIL

---

## Test Suite 3: UI & UX

### Test Case 3.1: Responsive Preview

**Objective:** Preview update realtime

**Steps:**
1. Open create/edit form
2. Ubah gaji pokok dari 5000000 → 6000000
3. ✅ **Expected:** Preview langsung update WITHOUT submit
4. Check/uncheck potongan
5. ✅ **Expected:** Preview langsung update

**Result:** ✅ PASS / ❌ FAIL

---

### Test Case 3.2: Currency Formatting

**Objective:** Format Rupiah benar

**Steps:**
1. Open form dengan nilai besar (misal: 15000000)
2. ✅ **Expected Preview:** "Rp 15,000,000" (dengan separator ribuan)

**Result:** ✅ PASS / ❌ FAIL

---

### Test Case 3.3: Error Message Display

**Objective:** Error messages tampil jelas

**Steps:**
1. Submit invalid form
2. ✅ **Expected:**
   - Error message muncul di bawah field yang bermasalah
   - Warna merah
   - Text jelas dan informatif

**Result:** ✅ PASS / ❌ FAIL

---

## Test Suite 4: Edge Cases

### Test Case 4.1: Zero Salary

**Objective:** Handle gaji = 0

**Steps:**
1. Create penggajian dengan gaji pokok = 0
2. ✅ **Expected:** 
   - Validation error: "Gaji pokok minimal 0" (or allowed if business rule)
   - Atau berhasil jika gaji 0 diizinkan

**Result:** ✅ PASS / ❌ FAIL

---

### Test Case 4.2: Very Large Numbers

**Objective:** Handle angka sangat besar

**Steps:**
1. Input gaji pokok: 999999999999 (12 digit)
2. ✅ **Expected:**
   - Accepted (within max limit)
   - Calculation correct
   - Display formatted

**Result:** ✅ PASS / ❌ FAIL

---

### Test Case 4.3: Negative Result Prevention

**Objective:** Gaji bersih tidak boleh negatif

**Steps:**
1. Create dengan:
   - Gaji Pokok: 1,000,000
   - Potongan total: 2,000,000
2. ✅ **Expected:**
   - Gaji Bersih: Rp 0 (not negative)
   - Or validation error tergantung business rule

**Result:** ✅ PASS / ❌ FAIL

---

## Test Suite 5: JavaScript Console Check

### Test Case 5.1: No JavaScript Errors

**Objective:** Tidak ada error di console

**Steps:**
1. Open browser console (F12)
2. Navigate ke create/edit form
3. Interact dengan semua features
4. ✅ **Expected:** No errors di console

**Result:** ✅ PASS / ❌ FAIL

**Console Output:**
_____________________________________________

---

## Test Suite 6: Cross-Browser Testing

### Test Case 6.1: Chrome

- ✅ PASS / ❌ FAIL
- Notes: __________________

### Test Case 6.2: Firefox

- ✅ PASS / ❌ FAIL
- Notes: __________________

### Test Case 6.3: Safari (if available)

- ✅ PASS / ❌ FAIL
- Notes: __________________

---

## Regression Testing Checklist

Pastikan fitur lain tidak terganggu:

- [ ] **Index Page** - List penggajian masih berfungsi
- [ ] **Show Page** - Detail penggajian masih tampil
- [ ] **Delete** - Hapus penggajian draft masih bisa
- [ ] **Status Update** - Update status masih berfungsi
- [ ] **Bulk Generate** - Generate massal tidak error
- [ ] **Export PDF** - Slip gaji masih generate
- [ ] **Laporan** - Laporan penggajian tidak error

---

## Troubleshooting

### Issue: Preview Tidak Update

**Solution:**
- Check browser console untuk JS errors
- Clear cache: Ctrl+Shift+R
- Verify jQuery/JavaScript loaded

### Issue: Validation Error "total_potongan required"

**Solution:**
- Check hidden input ada di HTML
- Check JavaScript calculateSalary() running
- Check console errors

### Issue: Auto-fill Tidak Berfungsi

**Solution:**
- Check data-attribute di option karyawan
- Check field name: `tunjangan_jabatan` not `tunjangan`
- Check JavaScript event listener

---

## Test Summary Template

| Test Suite | Total | Pass | Fail | Notes |
|------------|-------|------|------|-------|
| Create     | 6     | ____ | ____ | _____ |
| Edit       | 3     | ____ | ____ | _____ |
| UI/UX      | 3     | ____ | ____ | _____ |
| Edge Cases | 3     | ____ | ____ | _____ |
| Console    | 1     | ____ | ____ | _____ |
| Cross-Browser | 3  | ____ | ____ | _____ |
| **TOTAL**  | **19**| ____ | ____ | _____ |

---

## Sign-off

**Tested By:** _______________________
**Date:** _______________________
**Environment:** _______________________
**Overall Result:** ✅ APPROVED / ❌ NEED FIX

**Critical Issues Found:**
_____________________________________________
_____________________________________________

**Recommendation:**
- [ ] APPROVED for production
- [ ] Need minor fixes
- [ ] Need major fixes
- [ ] Block deployment

# 📋 Panduan Testing Notifikasi - Payroll System

## Overview

Dokumen ini berisi langkah-langkah testing manual untuk memastikan sistem notifikasi bekerja dengan baik di semua modul CRUD.

**Target Testing:**
- ✅ Notifikasi Success (Create, Update, Delete)
- ✅ Notifikasi Error (Validation, Business Logic)
- ✅ Auto-hide setelah 5 detik
- ✅ Tidak ada duplikasi
- ✅ Konsistensi pesan

---

## Persiapan Testing

### 1. Setup Environment
```bash
# Clear cache
php artisan cache:clear
php artisan view:clear
php artisan config:clear

# Ensure seeders run
php artisan db:seed
```

### 2. Browser Setup
- Open Chrome/Firefox dengan DevTools (F12)
- Console tab untuk check JS errors
- Network tab untuk check session
- Disable ad-blocker (may block Alpine.js CDN)

### 3. Login
```
Admin Account:
Email: admin@payroll.com
Password: password
```

---

## Test Suite 1: JABATAN Module

### Test 1.1: Create Success Notification

**Steps:**
1. Navigate ke `/jabatan/create`
2. Fill form:
   - Nama Jabatan: "Test Manager"
   - Gaji Pokok: 8000000
   - Tunjangan: 2000000
3. Click "Simpan"

**Expected Result:**
- ✅ Redirect ke `/jabatan`
- ✅ Green notification appears:
  - Message: "Data jabatan berhasil ditambahkan."
  - Icon: Green checkmark
  - Border: Green
- ✅ Auto-hide after 5 seconds
- ✅ Smooth fade-out animation

**Actual Result:** PASS / FAIL

**Notes:** _______________________

---

### Test 1.2: Update Success Notification

**Steps:**
1. From jabatan index, click "Edit" on any jabatan
2. Change "Nama Jabatan" to "Updated Name"
3. Click "Update"

**Expected Result:**
- ✅ Green notification: "Data jabatan berhasil diperbarui."
- ✅ Auto-hide after 5 seconds

**Actual Result:** PASS / FAIL

---

### Test 1.3: Delete Success Notification

**Steps:**
1. Create new jabatan without any karyawan
2. Click "Delete" button
3. Confirm deletion

**Expected Result:**
- ✅ Green notification: "Data jabatan berhasil dihapus."
- ✅ Jabatan removed from list

**Actual Result:** PASS / FAIL

---

### Test 1.4: Delete Error (Has Karyawan)

**Steps:**
1. Try to delete jabatan yang sudah dipakai karyawan
2. Click "Delete"

**Expected Result:**
- ✅ Red notification appears:
  - Message: "Jabatan tidak dapat dihapus karena masih digunakan oleh X karyawan aktif."
  - Icon: Red X
  - Border: Red
- ✅ Jabatan NOT deleted
- ✅ Auto-hide after 5 seconds

**Actual Result:** PASS / FAIL

---

### Test 1.5: Validation Errors

**Steps:**
1. Navigate ke `/jabatan/create`
2. Submit form EMPTY (no fill)
3. Observe errors

**Expected Result:**
- ✅ Form shows field-specific errors:
  - "Nama Jabatan wajib diisi."
  - "Gaji Pokok wajib diisi."
  - "Tunjangan Jabatan wajib diisi."
- ✅ Errors in RED text below each field
- ✅ No green success notification

**Actual Result:** PASS / FAIL

---

## Test Suite 2: KARYAWAN Module

### Test 2.1: Create Success Notification

**Steps:**
1. Navigate ke `/karyawan/create`
2. Fill all required fields
3. Click "Simpan"

**Expected Result:**
- ✅ Green notification: "Data karyawan berhasil ditambahkan."
- ✅ If no password provided: Additional text about default password
- ✅ Redirect to `/karyawan`

**Actual Result:** PASS / FAIL

---

### Test 2.2: Update Success Notification

**Steps:**
1. Edit any karyawan
2. Change alamat or telepon
3. Click "Update"

**Expected Result:**
- ✅ Green notification: "Data karyawan berhasil diperbarui."

**Actual Result:** PASS / FAIL

---

### Test 2.3: Delete Success Notification

**Steps:**
1. Create new karyawan (no penggajian history)
2. Delete the karyawan

**Expected Result:**
- ✅ Green notification: "Data karyawan berhasil dihapus."

**Actual Result:** PASS / FAIL

---

### Test 2.4: Delete Error (Has Penggajian)

**Steps:**
1. Try delete karyawan yang sudah punya riwayat penggajian
2. Click "Delete"

**Expected Result:**
- ✅ Red notification: "Karyawan tidak dapat dihapus karena memiliki riwayat penggajian. Ubah status menjadi nonaktif sebagai gantinya."
- ✅ Karyawan NOT deleted

**Actual Result:** PASS / FAIL

---

### Test 2.5: Validation - Duplicate NIK

**Steps:**
1. Create karyawan with NIK: "12345"
2. Try create another karyawan with same NIK

**Expected Result:**
- ✅ Validation error on NIK field
- ✅ Message: "NIK sudah terdaftar"

**Actual Result:** PASS / FAIL

---

## Test Suite 3: POTONGAN Module

### Test 3.1: Create Success Notification

**Steps:**
1. Navigate ke `/potongan/create`
2. Fill form (e.g., BPJS 2%)
3. Submit

**Expected Result:**
- ✅ Green notification: "Data potongan berhasil ditambahkan."

**Actual Result:** PASS / FAIL

---

### Test 3.2: Update Success Notification

**Steps:**
1. Edit any potongan
2. Change nilai from 2% to 3%
3. Submit

**Expected Result:**
- ✅ Green notification: "Data potongan berhasil diperbarui."

**Actual Result:** PASS / FAIL

---

### Test 3.3: Delete Success Notification

**Steps:**
1. Create new potongan (not used in any penggajian)
2. Delete it

**Expected Result:**
- ✅ Green notification: "Data potongan berhasil dihapus."

**Actual Result:** PASS / FAIL

---

### Test 3.4: Delete Error (Already Used)

**Steps:**
1. Try delete potongan yang sudah digunakan
2. Click "Delete"

**Expected Result:**
- ✅ Red notification: "Potongan tidak dapat dihapus karena sudah pernah digunakan dalam penggajian. Nonaktifkan potongan sebagai gantinya."
- ✅ Potongan NOT deleted

**Actual Result:** PASS / FAIL

---

## Test Suite 4: PENGGAJIAN Module

### Test 4.1: Create Success Notification

**Steps:**
1. Navigate ke `/penggajian/create`
2. Select karyawan, periode, check some potongan
3. Submit

**Expected Result:**
- ✅ Green notification: "Data penggajian berhasil ditambahkan."
- ✅ Redirect to `/penggajian`

**Actual Result:** PASS / FAIL

---

### Test 4.2: Update Success Notification

**Steps:**
1. Edit draft penggajian
2. Change periode or potongan
3. Submit

**Expected Result:**
- ✅ Green notification: "Data penggajian berhasil diperbarui."

**Actual Result:** PASS / FAIL

---

### Test 4.3: Delete Success Notification

**Steps:**
1. Delete draft penggajian
2. Confirm

**Expected Result:**
- ✅ Green notification: "Data penggajian berhasil dihapus."

**Actual Result:** PASS / FAIL

---

### Test 4.4: Edit Error (Locked Status)

**Steps:**
1. Create penggajian with status "Final"
2. Try to access edit page

**Expected Result:**
- ✅ Redirect to show page
- ✅ Red notification: "Penggajian dengan status final tidak dapat diubah. Ubah status menjadi draft terlebih dahulu."

**Actual Result:** PASS / FAIL

---

### Test 4.5: Delete Error (Locked Status)

**Steps:**
1. Try delete penggajian with status "Dibayar"

**Expected Result:**
- ✅ Red notification: "Penggajian dengan status dibayar tidak dapat dihapus."
- ✅ Penggajian NOT deleted

**Actual Result:** PASS / FAIL

---

### Test 4.6: Bulk Generate Success

**Steps:**
1. Navigate to `/penggajian/generate-bulk`
2. Select periode
3. Submit

**Expected Result:**
- ✅ Green notification: "Berhasil membuat X penggajian. Dilewati: Y (sudah ada)."
- ✅ Show count of created and skipped

**Actual Result:** PASS / FAIL

---

### Test 4.7: Update Status Success

**Steps:**
1. Change penggajian status from Draft to Final
2. Submit

**Expected Result:**
- ✅ Green notification: "Status penggajian berhasil diubah menjadi final."

**Actual Result:** PASS / FAIL

---

### Test 4.8: Validation - Duplicate Karyawan+Periode

**Steps:**
1. Create penggajian: Karyawan A, Periode 2026-07
2. Try create again: Same karyawan, same periode

**Expected Result:**
- ✅ Validation error
- ✅ Message: "Data penggajian untuk karyawan dan periode ini sudah ada."

**Actual Result:** PASS / FAIL

---

## Test Suite 5: UI/UX Checks

### Test 5.1: Auto-Hide Timer

**Steps:**
1. Trigger any success notification
2. Wait and observe

**Expected Result:**
- ✅ Notification visible for ~5 seconds
- ✅ Smooth fade-out animation
- ✅ Completely hidden after animation

**Actual Result:** PASS / FAIL

---

### Test 5.2: Manual Close Button

**Steps:**
1. Trigger notification
2. Click X button immediately

**Expected Result:**
- ✅ Notification closes instantly
- ✅ No errors in console

**Actual Result:** PASS / FAIL

---

### Test 5.3: No Duplicate Notifications

**Steps:**
1. Create jabatan
2. Observe notification count

**Expected Result:**
- ✅ Only ONE notification appears
- ✅ No duplicate messages

**Actual Result:** PASS / FAIL

---

### Test 5.4: Refresh Clears Notification

**Steps:**
1. See success notification
2. Refresh page (F5)

**Expected Result:**
- ✅ Notification GONE after refresh
- ✅ Not persistent across page loads

**Actual Result:** PASS / FAIL

---

### Test 5.5: Back Button No Notification

**Steps:**
1. Create jabatan → see notification
2. Navigate elsewhere
3. Press browser back button

**Expected Result:**
- ✅ NO notification on back
- ✅ Flash message only shows once

**Actual Result:** PASS / FAIL

---

## Test Suite 6: Cross-Browser Testing

### Test 6.1: Chrome

- [ ] Success notifications work
- [ ] Error notifications work
- [ ] Auto-hide works
- [ ] Animations smooth
- [ ] No console errors

**Result:** PASS / FAIL

---

### Test 6.2: Firefox

- [ ] Success notifications work
- [ ] Error notifications work
- [ ] Auto-hide works
- [ ] Animations smooth
- [ ] No console errors

**Result:** PASS / FAIL

---

### Test 6.3: Safari (if available)

- [ ] Success notifications work
- [ ] Error notifications work
- [ ] Auto-hide works
- [ ] Animations smooth
- [ ] No console errors

**Result:** PASS / FAIL

---

## Test Suite 7: Error Scenarios

### Test 7.1: General Exception

**Steps:**
1. Temporarily break database connection
2. Try create jabatan

**Expected Result:**
- ✅ Red notification: "Terjadi kesalahan saat menyimpan data jabatan: [error detail]"
- ✅ Form retains input (withInput)

**Actual Result:** PASS / FAIL

---

### Test 7.2: Multiple Validation Errors

**Steps:**
1. Submit create form with:
   - Empty nama
   - Empty gaji pokok
   - Invalid email format (for karyawan)

**Expected Result:**
- ✅ ALL errors displayed under respective fields
- ✅ No green notification
- ✅ Red text for each error

**Actual Result:** PASS / FAIL

---

## Debugging Checklist

### If Notifications Don't Appear

- [ ] Check Alpine.js loaded: `window.Alpine` in console
- [ ] Check session middleware active
- [ ] Check `flash-message.blade.php` included in layout
- [ ] Check browser console for JS errors
- [ ] Verify redirect has `->with('success', 'message')`

### If Auto-Hide Doesn't Work

- [ ] Check Alpine.js `x-init` directive
- [ ] Check `setTimeout` value (should be 5000)
- [ ] Check browser console for errors
- [ ] Verify `x-show` directive

### If Validation Errors Not Showing

- [ ] Check `@error` directive in blade
- [ ] Check FormRequest validation rules
- [ ] Check `withErrors()` on redirect
- [ ] Verify error bag name

---

## Test Summary Template

### Module: ___________

| Test Case | Expected | Actual | Status | Notes |
|-----------|----------|--------|--------|-------|
| Create Success | Green notification | _____ | PASS/FAIL | _____ |
| Update Success | Green notification | _____ | PASS/FAIL | _____ |
| Delete Success | Green notification | _____ | PASS/FAIL | _____ |
| Delete Error | Red notification | _____ | PASS/FAIL | _____ |
| Validation | Field errors | _____ | PASS/FAIL | _____ |
| Auto-hide | Disappear 5s | _____ | PASS/FAIL | _____ |
| No Duplicate | Single alert | _____ | PASS/FAIL | _____ |

### Overall Summary

| Module | Tests | Pass | Fail | Notes |
|--------|-------|------|------|-------|
| Jabatan | 5 | ____ | ____ | _____ |
| Karyawan | 5 | ____ | ____ | _____ |
| Potongan | 4 | ____ | ____ | _____ |
| Penggajian | 8 | ____ | ____ | _____ |
| UI/UX | 5 | ____ | ____ | _____ |
| Cross-Browser | 3 | ____ | ____ | _____ |
| Error Scenarios | 2 | ____ | ____ | _____ |
| **TOTAL** | **32** | ____ | ____ | _____ |

---

## Sign-off

**Tested By:** _______________________

**Date:** _______________________

**Environment:** _______________________

**Browser:** _______________________ Version: _______

**Overall Result:** 
- [ ] ✅ ALL PASS - Approved for production
- [ ] ⚠️ Minor issues - Need fixes
- [ ] ❌ FAIL - Block deployment

**Critical Issues Found:**
_____________________________________________
_____________________________________________

**Recommendations:**
_____________________________________________
_____________________________________________

---

## Quick Reference

### Message Standards

```php
// CREATE
->with('success', 'Data [module] berhasil ditambahkan.')

// UPDATE  
->with('success', 'Data [module] berhasil diperbarui.')

// DELETE
->with('success', 'Data [module] berhasil dihapus.')

// ERROR
->with('error', 'Terjadi kesalahan saat [operasi]: ' . $e->getMessage())
```

### Testing Commands

```bash
# Clear everything
php artisan optimize:clear

# Check routes
php artisan route:list | grep jabatan
php artisan route:list | grep karyawan
php artisan route:list | grep potongan
php artisan route:list | grep penggajian

# Check session
php artisan tinker
>>> session()->all()

# Tail logs
tail -f storage/logs/laravel.log
```

---

## Conclusion

Testing notifikasi memastikan:
- ✅ User mendapat feedback jelas setiap operasi
- ✅ Error handling yang baik
- ✅ UX yang smooth dan professional
- ✅ Konsistensi di semua modul

**Target:** 100% test pass rate

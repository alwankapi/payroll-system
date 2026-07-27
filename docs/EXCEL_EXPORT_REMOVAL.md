# Penghapusan Fitur Export Excel

## Tanggal
27 Juli 2026

## Deskripsi
Fitur export Excel telah dihapus dari sistem laporan penggajian. Sekarang sistem hanya mendukung export ke PDF.

## Perubahan yang Dilakukan

### 1. View Laporan (`resources/views/laporan/index.blade.php`)
- ✅ Menghapus tombol "Export Excel" dari halaman laporan
- ✅ Hanya tersisa tombol "Export PDF"

### 2. Routes (`routes/web.php`)
- ✅ Menghapus route `laporan.exportExcel`
- ✅ Route `/export-excel` tidak lagi tersedia

### 3. Controller (`app/Http/Controllers/LaporanController.php`)
- ✅ Menghapus method `exportExcel()`
- ✅ Menghapus import `use Maatwebsite\Excel\Facades\Excel;`
- ✅ Menghapus import `use App\Exports\LaporanPenggajianExport;`

## File yang Tidak Digunakan

File berikut masih ada di sistem tetapi tidak lagi digunakan:
- `app/Exports/LaporanPenggajianExport.php` - Class export Excel

File ini dapat dihapus di masa mendatang jika dipastikan tidak ada fitur lain yang menggunakannya.

## Fitur yang Masih Tersedia

- ✅ Export Laporan ke PDF
- ✅ Filter laporan (bulan, tahun, jabatan, status)
- ✅ Summary statistik laporan

## Catatan

Package `maatwebsite/excel` masih terinstall di composer. Jika ingin membersihkan dependency yang tidak digunakan, dapat menjalankan:
```bash
composer remove maatwebsite/excel
```

Namun perlu dipastikan terlebih dahulu bahwa tidak ada fitur lain yang menggunakan package ini.

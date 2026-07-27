<?php

namespace App\Http\Controllers;

use App\Models\Penggajian;
use App\Models\Jabatan;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanPenggajianExport;

class LaporanController extends Controller
{
    /**
     * Display laporan penggajian
     */
    public function index(Request $request)
    {
        $query = Penggajian::with(['karyawan.jabatan', 'details.potongan']);

        // Filter bulan
        if ($request->filled('bulan')) {
            $query->whereMonth('periode', $request->bulan);
        }

        // Filter tahun
        if ($request->filled('tahun')) {
            $query->whereYear('periode', $request->tahun);
        }

        // Filter jabatan
        if ($request->filled('jabatan_id')) {
            $query->whereHas('karyawan', function ($q) use ($request) {
                $q->where('jabatan_id', $request->jabatan_id);
            });
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Get data
        $penggajians = $query->orderBy('periode', 'desc')->get();

        // Hitung ringkasan
        $summary = [
            'total_gaji_pokok' => $penggajians->sum('gaji_pokok'),
            'total_tunjangan' => $penggajians->sum('tunjangan'),
            'total_potongan' => $penggajians->sum('total_potongan'),
            'total_gaji_bersih' => $penggajians->sum('gaji_bersih'),
            'jumlah_karyawan' => $penggajians->unique('karyawan_id')->count(),
            'jumlah_transaksi' => $penggajians->count(),
        ];

        // Get jabatan untuk filter
        $jabatans = Jabatan::orderBy('nama_jabatan')->get();

        return view('laporan.index', compact('penggajians', 'summary', 'jabatans'));
    }

    /**
     * Export laporan ke PDF
     */
    public function exportPdf(Request $request)
    {
        // Eager load all necessary relationships to prevent N+1 queries
        $query = Penggajian::with(['karyawan.jabatan', 'details.potongan']);

        // Apply filters
        if ($request->filled('bulan')) {
            $query->whereMonth('periode', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->whereYear('periode', $request->tahun);
        }
        if ($request->filled('jabatan_id')) {
            $query->whereHas('karyawan', function ($q) use ($request) {
                $q->where('jabatan_id', $request->jabatan_id);
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $penggajians = $query->orderBy('periode', 'desc')
                            ->orderBy('created_at', 'desc')
                            ->get();

        $summary = [
            'total_gaji_pokok' => $penggajians->sum('gaji_pokok'),
            'total_tunjangan' => $penggajians->sum('tunjangan'),
            'total_potongan' => $penggajians->sum('total_potongan'),
            'total_gaji_bersih' => $penggajians->sum('gaji_bersih'),
            'jumlah_karyawan' => $penggajians->unique('karyawan_id')->count(),
            'jumlah_transaksi' => $penggajians->count(),
        ];

        $filters = [
            'bulan' => $request->bulan ? date('F', mktime(0, 0, 0, $request->bulan, 1)) : 'Semua',
            'tahun' => $request->tahun ?? 'Semua',
            'jabatan' => $request->filled('jabatan_id') ? Jabatan::find($request->jabatan_id)->nama_jabatan : 'Semua',
            'status' => $request->status ? ucfirst($request->status) : 'Semua',
        ];

        // Generate PDF dengan paper size A4 Portrait
        $pdf = Pdf::loadView('pdf.laporan-penggajian', compact('penggajians', 'summary', 'filters'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'debugKeepTemp' => false,
                'debugCss' => false,
                'enable_font_subsetting' => true,
                'dpi' => 150,
            ]);

        $filename = 'laporan-penggajian-' . date('Ymd-His') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Export laporan ke Excel
     */
    public function exportExcel(Request $request)
    {
        $filters = [
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
            'jabatan_id' => $request->jabatan_id,
            'status' => $request->status,
        ];

        $filename = 'laporan-penggajian-' . date('Y-m-d-His') . '.xlsx';
        
        return Excel::download(new LaporanPenggajianExport($filters), $filename);
    }
}

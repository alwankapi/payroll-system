<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Penggajian;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class RiwayatGajiController extends Controller
{
    /**
     * Display riwayat penggajian karyawan.
     */
    public function index(Request $request)
    {
        $karyawan = $request->user()->karyawan;

        // Query penggajian milik karyawan ini saja
        $query = Penggajian::where('karyawan_id', $karyawan->id)
            ->with(['karyawan.jabatan']);

        // Filter berdasarkan bulan
        if ($request->filled('bulan')) {
            $query->whereMonth('periode', $request->bulan);
        }

        // Filter berdasarkan tahun
        if ($request->filled('tahun')) {
            $query->whereYear('periode', $request->tahun);
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Sorting terbaru
        $penggajians = $query->orderBy('periode', 'desc')->paginate(10);

        return view('karyawan.riwayat-gaji.index', compact('penggajians', 'karyawan'));
    }

    /**
     * Display detail slip gaji.
     */
    public function show(Request $request, Penggajian $penggajian)
    {
        // Authorization: pastikan penggajian milik karyawan ini
        if ($penggajian->karyawan_id !== $request->user()->karyawan->id) {
            abort(403, 'Anda tidak memiliki akses ke slip gaji ini.');
        }

        $penggajian->load(['karyawan.jabatan']);

        return view('karyawan.riwayat-gaji.show', compact('penggajian'));
    }

    /**
     * Download slip gaji as PDF.
     */
    public function download(Request $request, Penggajian $penggajian)
    {
        // Authorization: pastikan penggajian milik karyawan ini
        if ($penggajian->karyawan_id !== $request->user()->karyawan->id) {
            abort(403, 'Anda tidak memiliki akses ke slip gaji ini.');
        }

        // Hanya bisa download jika status final atau dibayar
        if (!in_array($penggajian->status, ['final', 'dibayar'])) {
            return back()->with('error', 'Slip gaji hanya dapat didownload jika status sudah Final atau Dibayar.');
        }

        $penggajian->load(['karyawan.jabatan']);

        // Generate PDF
        $pdf = Pdf::loadView('karyawan.riwayat-gaji.pdf', compact('penggajian'));
        
        $filename = 'Slip_Gaji_' . $penggajian->karyawan->nama_lengkap . '_' 
                    . $penggajian->periode->format('F_Y') . '.pdf';

        return $pdf->download($filename);
    }
}

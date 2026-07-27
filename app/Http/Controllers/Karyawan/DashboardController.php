<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display karyawan dashboard.
     */
    public function index(Request $request)
    {
        $karyawan = $request->user()->karyawan;
        
        // Load relasi yang dibutuhkan
        $karyawan->load(['jabatan', 'penggajians' => function ($query) {
            $query->orderBy('periode', 'desc');
        }]);

        // Ambil penggajian terakhir
        $latestPenggajian = $karyawan->penggajians->first();

        // Hitung statistik
        $stats = [
            'total_gaji_bulan_ini' => $latestPenggajian ? $latestPenggajian->gaji_pokok + $latestPenggajian->tunjangan : 0,
            'total_potongan_bulan_ini' => $latestPenggajian ? $latestPenggajian->total_potongan : 0,
            'gaji_bersih_bulan_ini' => $latestPenggajian ? $latestPenggajian->gaji_bersih : 0,
            'jumlah_histori' => $karyawan->penggajians->count(),
        ];

        return view('karyawan.dashboard', compact('karyawan', 'latestPenggajian', 'stats'));
    }
}

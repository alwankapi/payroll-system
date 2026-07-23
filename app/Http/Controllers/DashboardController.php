<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\Penggajian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display dashboard with statistics and widgets
     */
    public function index()
    {
        // Get current month for filtering
        $currentMonth = Carbon::now()->format('Y-m-01');
        
        // Statistics Cards
        $totalKaryawan = Karyawan::count();
        $totalJabatan = Jabatan::count();
        $totalPenggajian = Penggajian::count();
        
        // Total gaji bulan ini (sum of gaji_bersih)
        $totalGajiBulanIni = Penggajian::whereYear('periode', Carbon::now()->year)
            ->whereMonth('periode', Carbon::now()->month)
            ->sum('gaji_bersih');
        
        // Total potongan bulan ini
        $totalPotonganBulanIni = Penggajian::whereYear('periode', Carbon::now()->year)
            ->whereMonth('periode', Carbon::now()->month)
            ->sum('total_potongan');
        
        // Penggajian terbaru (latest 5)
        $penggajianTerbaru = Penggajian::with(['karyawan.jabatan'])
            ->latest()
            ->take(5)
            ->get();
        
        // Karyawan terbaru (latest 5)
        $karyawanTerbaru = Karyawan::with('jabatan')
            ->latest()
            ->take(5)
            ->get();
        
        // Chart data: Penggajian per bulan (last 6 months)
        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $total = Penggajian::whereYear('periode', $date->year)
                ->whereMonth('periode', $date->month)
                ->sum('gaji_bersih');
            
            $chartData[] = [
                'month' => $date->translatedFormat('M Y'),
                'total' => $total,
            ];
        }
        
        // Statistik status penggajian
        $statusStats = [
            'draft' => Penggajian::where('status', 'draft')->count(),
            'final' => Penggajian::where('status', 'final')->count(),
            'dibayar' => Penggajian::where('status', 'dibayar')->count(),
        ];
        
        return view('dashboard', compact(
            'totalKaryawan',
            'totalJabatan',
            'totalPenggajian',
            'totalGajiBulanIni',
            'totalPotonganBulanIni',
            'penggajianTerbaru',
            'karyawanTerbaru',
            'chartData',
            'statusStats'
        ));
    }
}

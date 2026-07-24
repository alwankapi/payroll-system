<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\Penggajian;
use App\Models\Potongan;
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
        
        // Top 5 Jabatan dengan Total Gaji Terbesar (bulan ini)
        $topJabatan = DB::table('penggajians')
            ->join('karyawans', 'penggajians.karyawan_id', '=', 'karyawans.id')
            ->join('jabatans', 'karyawans.jabatan_id', '=', 'jabatans.id')
            ->whereYear('penggajians.periode', Carbon::now()->year)
            ->whereMonth('penggajians.periode', Carbon::now()->month)
            ->select('jabatans.nama_jabatan', DB::raw('SUM(penggajians.gaji_bersih) as total_gaji'))
            ->groupBy('jabatans.id', 'jabatans.nama_jabatan')
            ->orderByDesc('total_gaji')
            ->take(5)
            ->get();
        
        // Top 5 Potongan Terbesar (aktif)
        $topPotongan = Potongan::where('is_active', true)
            ->orderByDesc(DB::raw('CASE WHEN jenis_potongan = "nominal" THEN nilai ELSE 0 END'))
            ->take(5)
            ->get();
        
        // Recent Activities (10 latest activities)
        $recentActivities = collect([]);
        
        // Ambil penggajian terbaru dengan status changes
        $recentPenggajian = Penggajian::with(['karyawan'])
            ->latest('updated_at')
            ->take(5)
            ->get()
            ->map(function($item) {
                return [
                    'type' => 'penggajian',
                    'action' => 'updated',
                    'description' => "Penggajian {$item->karyawan->nama_lengkap} - Status: {$item->status}",
                    'time' => $item->updated_at,
                ];
            });
        
        // Ambil karyawan terbaru
        $recentKaryawanActivities = Karyawan::latest('created_at')
            ->take(5)
            ->get()
            ->map(function($item) {
                return [
                    'type' => 'karyawan',
                    'action' => 'created',
                    'description' => "Karyawan baru ditambahkan: {$item->nama_lengkap}",
                    'time' => $item->created_at,
                ];
            });
        
        // Gabungkan dan sort by time
        $recentActivities = $recentPenggajian->concat($recentKaryawanActivities)
            ->sortByDesc('time')
            ->take(10);
        
        return view('dashboard', compact(
            'totalKaryawan',
            'totalJabatan',
            'totalPenggajian',
            'totalGajiBulanIni',
            'totalPotonganBulanIni',
            'penggajianTerbaru',
            'karyawanTerbaru',
            'chartData',
            'statusStats',
            'topJabatan',
            'topPotongan',
            'recentActivities'
        ));
    }
}

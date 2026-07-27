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
     * 
     * FR-07: Dashboard terpisah untuk Admin dan Karyawan
     * Admin: Lihat semua data dan statistik lengkap
     * Karyawan: Hanya lihat data penggajian milik sendiri (BR-02)
     */
    public function index()
    {
        $user = auth()->user();
        
        // Jika Karyawan, redirect ke dashboard karyawan
        if ($user->role === 'karyawan') {
            return redirect()->route('karyawan.dashboard');
        }
        
        // Admin Dashboard - Get current month for filtering
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;
        
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
        
        // Top 5 Karyawan dengan Potongan Alpha Terbesar bulan ini
        $topPotongan = Penggajian::with(['karyawan'])
            ->whereYear('periode', $currentYear)
            ->whereMonth('periode', $currentMonth)
            ->orderByDesc('potongan_alpha')
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

    /**
     * Dashboard khusus untuk Karyawan
     * 
     * Menampilkan hanya data penggajian milik karyawan yang login (BR-02, FR-07, FR-12)
     */
    private function karyawanDashboard()
    {
        $user = auth()->user();
        
        // Pastikan user punya data karyawan
        if (!$user->karyawan) {
            return view('dashboard')->with('error', 'Data karyawan tidak ditemukan untuk akun Anda.');
        }
        
        $karyawan = $user->karyawan->load('jabatan');
        
        // Statistik penggajian karyawan ini
        $totalPenggajian = Penggajian::where('karyawan_id', $karyawan->id)->count();
        
        // Total gaji yang sudah diterima (status dibayar)
        $totalGajiDiterima = Penggajian::where('karyawan_id', $karyawan->id)
            ->where('status', 'dibayar')
            ->sum('gaji_bersih');
        
        // Gaji bulan ini
        $gajiBulanIni = Penggajian::where('karyawan_id', $karyawan->id)
            ->whereYear('periode', Carbon::now()->year)
            ->whereMonth('periode', Carbon::now()->month)
            ->first();
        
        // Riwayat penggajian (last 6 months)
        $riwayatPenggajian = Penggajian::where('karyawan_id', $karyawan->id)
            ->orderBy('periode', 'desc')
            ->take(6)
            ->get();
        
        // Chart data: Gaji per bulan (last 6 months)
        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $penggajian = Penggajian::where('karyawan_id', $karyawan->id)
                ->whereYear('periode', $date->year)
                ->whereMonth('periode', $date->month)
                ->first();
            
            $chartData[] = [
                'month' => $date->translatedFormat('M Y'),
                'total' => $penggajian ? $penggajian->gaji_bersih : 0,
            ];
        }
        
        // Statistik status penggajian karyawan ini
        $statusStats = [
            'draft' => Penggajian::where('karyawan_id', $karyawan->id)->where('status', 'draft')->count(),
            'final' => Penggajian::where('karyawan_id', $karyawan->id)->where('status', 'final')->count(),
            'dibayar' => Penggajian::where('karyawan_id', $karyawan->id)->where('status', 'dibayar')->count(),
        ];
        
        return view('dashboard-karyawan', compact(
            'karyawan',
            'totalPenggajian',
            'totalGajiDiterima',
            'gajiBulanIni',
            'riwayatPenggajian',
            'chartData',
            'statusStats'
        ));
    }
}

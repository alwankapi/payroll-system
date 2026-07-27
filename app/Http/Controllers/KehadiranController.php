<?php

namespace App\Http\Controllers;

use App\Models\Kehadiran;
use App\Models\Karyawan;
use App\Http\Requests\StoreKehadiranRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KehadiranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Kehadiran::with(['karyawan.jabatan']);

        // Filter by bulan/tahun
        if ($request->filled('bulan') && $request->filled('tahun')) {
            $query->byMonth($request->tahun, $request->bulan);
        }

        // Filter by karyawan
        if ($request->filled('karyawan_id')) {
            $query->where('karyawan_id', $request->karyawan_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        $kehadirans = $query->orderBy('tanggal', 'desc')
                           ->paginate(20)
                           ->withQueryString();

        $karyawans = Karyawan::with('jabatan')
                            ->whereIn('status_karyawan', ['tetap', 'kontrak', 'magang'])
                            ->orderBy('nama_lengkap')
                            ->get();

        return view('kehadiran.index', compact('kehadirans', 'karyawans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $karyawans = Karyawan::with('jabatan')
                            ->whereIn('status_karyawan', ['tetap', 'kontrak', 'magang'])
                            ->orderBy('nama_lengkap')
                            ->get();

        return view('kehadiran.create', compact('karyawans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreKehadiranRequest $request)
    {
        try {
            Kehadiran::create($request->validated());

            return redirect()
                ->route('kehadiran.index')
                ->with('success', 'Data kehadiran berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan data kehadiran: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Kehadiran $kehadiran)
    {
        $kehadiran->load(['karyawan.jabatan']);
        
        return view('kehadiran.show', compact('kehadiran'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kehadiran $kehadiran)
    {
        $karyawans = Karyawan::with('jabatan')
                            ->whereIn('status_karyawan', ['tetap', 'kontrak', 'magang'])
                            ->orderBy('nama_lengkap')
                            ->get();

        return view('kehadiran.edit', compact('kehadiran', 'karyawans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreKehadiranRequest $request, Kehadiran $kehadiran)
    {
        try {
            $kehadiran->update($request->validated());

            return redirect()
                ->route('kehadiran.index')
                ->with('success', 'Data kehadiran berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui data kehadiran: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kehadiran $kehadiran)
    {
        try {
            $kehadiran->delete();

            return redirect()
                ->route('kehadiran.index')
                ->with('success', 'Data kehadiran berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus data kehadiran: ' . $e->getMessage());
        }
    }

    /**
     * Rekap kehadiran per karyawan per bulan
     */
    public function rekap(Request $request)
    {
        $tahun = $request->input('tahun', date('Y'));
        $bulan = $request->input('bulan', date('m'));

        $karyawans = Karyawan::with(['jabatan', 'kehadirans' => function ($query) use ($tahun, $bulan) {
            $query->byMonth($tahun, $bulan)->orderBy('tanggal');
        }])
        ->whereIn('status_karyawan', ['tetap', 'kontrak', 'magang'])
        ->orderBy('nama_lengkap')
        ->get();

        // Hitung statistik per karyawan
        $rekapData = $karyawans->map(function ($karyawan) {
            $kehadirans = $karyawan->kehadirans;
            
            return [
                'karyawan' => $karyawan,
                'total_hari' => $kehadirans->count(),
                'hadir' => $kehadirans->where('status', 'hadir')->count(),
                'izin' => $kehadirans->where('status', 'izin')->count(),
                'sakit' => $kehadirans->where('status', 'sakit')->count(),
                'alpha' => $kehadirans->where('status', 'alpha')->count(),
            ];
        });

        return view('kehadiran.rekap', compact('rekapData', 'tahun', 'bulan'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Penggajian;
use App\Services\SlipGajiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SlipGajiController extends Controller
{
    protected SlipGajiService $slipGajiService;

    public function __construct(SlipGajiService $slipGajiService)
    {
        $this->slipGajiService = $slipGajiService;
    }

    /**
     * Download slip gaji sebagai PDF
     * 
     * Authorization: Admin dapat akses semua, Karyawan hanya milik sendiri (BR-02, FR-32, FR-33)
     */
    public function download(Penggajian $penggajian)
    {
        // Authorization manual: admin atau karyawan yang bersangkutan
        $user = auth()->user();
        
        // Admin bisa akses semua slip gaji
        if ($user->role !== 'admin') {
            // Karyawan hanya bisa akses slip gaji milik sendiri
            if (!$user->karyawan || $user->karyawan->id !== $penggajian->karyawan_id) {
                abort(403, 'Anda tidak memiliki akses ke slip gaji ini.');
            }
        }

        // Validasi: hanya penggajian final/dibayar yang bisa dicetak
        if (!$this->slipGajiService->canPrintSlipGaji($penggajian)) {
            return back()->with('error', 'Hanya penggajian dengan status Final atau Dibayar yang dapat dicetak.');
        }

        try {
            return $this->slipGajiService->downloadSlipGaji($penggajian);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengunduh slip gaji: ' . $e->getMessage());
        }
    }

    /**
     * Stream/preview slip gaji di browser
     * 
     * Authorization: Admin dapat akses semua, Karyawan hanya milik sendiri (BR-02, FR-32, FR-33)
     */
    public function preview(Penggajian $penggajian)
    {
        // Authorization manual: admin atau karyawan yang bersangkutan
        $user = auth()->user();
        
        // Admin bisa akses semua slip gaji
        if ($user->role !== 'admin') {
            // Karyawan hanya bisa akses slip gaji milik sendiri
            if (!$user->karyawan || $user->karyawan->id !== $penggajian->karyawan_id) {
                abort(403, 'Anda tidak memiliki akses ke slip gaji ini.');
            }
        }

        // Validasi: hanya penggajian final/dibayar yang bisa dicetak
        if (!$this->slipGajiService->canPrintSlipGaji($penggajian)) {
            return back()->with('error', 'Hanya penggajian dengan status Final atau Dibayar yang dapat dicetak.');
        }

        try {
            return $this->slipGajiService->streamSlipGaji($penggajian);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menampilkan slip gaji: ' . $e->getMessage());
        }
    }
}

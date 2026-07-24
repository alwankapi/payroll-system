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
     */
    public function download(Penggajian $penggajian)
    {
        // Authorize: admin atau karyawan yang bersangkutan
        Gate::authorize('view', $penggajian);

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
     */
    public function preview(Penggajian $penggajian)
    {
        // Authorize: admin atau karyawan yang bersangkutan
        Gate::authorize('view', $penggajian);

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

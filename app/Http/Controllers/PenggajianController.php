<?php

namespace App\Http\Controllers;

use App\Models\Penggajian;
use App\Models\Karyawan;
use App\Http\Requests\StorePenggajianRequest;
use App\Http\Requests\UpdatePenggajianRequest;
use App\Services\PenggajianService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class PenggajianController extends Controller
{
    /**
     * Service untuk menangani logika bisnis penggajian.
     */
    protected PenggajianService $penggajianService;

    /**
     * Constructor dengan dependency injection.
     */
    public function __construct(PenggajianService $penggajianService)
    {
        $this->penggajianService = $penggajianService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        // Eager loading relasi karyawan dan jabatan
        $penggajians = Penggajian::with(['karyawan.jabatan'])
            ->when(request('periode'), function ($query, $periode) {
                return $query->whereYear('periode', date('Y', strtotime($periode)))
                            ->whereMonth('periode', date('m', strtotime($periode)));
            })
            ->when(request('status'), function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when(request('karyawan_id'), function ($query, $karyawanId) {
                return $query->where('karyawan_id', $karyawanId);
            })
            ->orderBy('periode', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        // Data untuk filter
        $karyawans = Karyawan::with('jabatan')
            ->orderBy('nama_lengkap', 'asc')
            ->get();

        return view('penggajian.index', compact('penggajians', 'karyawans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // Ambil karyawan aktif dengan relasi jabatan
        $karyawans = Karyawan::with('jabatan')
            ->where('status_karyawan', 'aktif')
            ->orderBy('nama_lengkap', 'asc')
            ->get();

        // Ambil semua potongan untuk checkbox
        $potongans = \App\Models\Potongan::orderBy('nama_potongan', 'asc')->get();

        return view('penggajian.create', compact('karyawans', 'potongans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePenggajianRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            // Gunakan service untuk membuat penggajian
            // Service akan menangani perhitungan gaji dan pembuatan detail potongan
            $penggajian = $this->penggajianService->createPenggajian($request->validated());

            DB::commit();

            return redirect()
                ->route('penggajian.index')
                ->with('success', 'Data penggajian berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat membuat data penggajian: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Penggajian $penggajian): View
    {
        // Eager loading semua relasi yang dibutuhkan untuk slip gaji
        $penggajian->load([
            'karyawan.user',
            'karyawan.jabatan',
            'details.potongan'
        ]);

        return view('penggajian.show', compact('penggajian'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Penggajian $penggajian): View
    {
        // BR-06: Penggajian Final/Dibayar tidak dapat diubah langsung
        if ($penggajian->is_terkunci) {
            return redirect()
                ->route('penggajian.show', $penggajian)
                ->with('error', 'Penggajian dengan status ' . $penggajian->status . ' tidak dapat diubah. Ubah status menjadi draft terlebih dahulu.');
        }

        $karyawans = Karyawan::with('jabatan')
            ->where('status_karyawan', 'aktif')
            ->orderBy('nama_lengkap', 'asc')
            ->get();

        // Ambil semua potongan untuk checkbox
        $potongans = \App\Models\Potongan::orderBy('nama_potongan', 'asc')->get();

        $penggajian->load(['karyawan', 'potongans']);

        return view('penggajian.edit', compact('penggajian', 'karyawans', 'potongans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePenggajianRequest $request, Penggajian $penggajian): RedirectResponse
    {
        try {
            // BR-06: Penggajian Final/Dibayar tidak dapat diubah
            if ($penggajian->is_terkunci) {
                return redirect()
                    ->back()
                    ->with('error', 'Penggajian dengan status ' . $penggajian->status . ' tidak dapat diubah.');
            }

            DB::beginTransaction();

            // Gunakan service untuk update penggajian
            // Service akan menangani perhitungan ulang gaji dan update detail potongan
            $penggajian = $this->penggajianService->updatePenggajian($penggajian, $request->validated());

            DB::commit();

            return redirect()
                ->route('penggajian.index')
                ->with('success', 'Data penggajian berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui data penggajian: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Penggajian $penggajian): RedirectResponse
    {
        try {
            // BR-24: Data penggajian berstatus Draft dapat dihapus; Final/Dibayar tidak
            if ($penggajian->is_terkunci) {
                return redirect()
                    ->back()
                    ->with('error', 'Penggajian dengan status ' . $penggajian->status . ' tidak dapat dihapus.');
            }

            DB::beginTransaction();

            // Hapus detail penggajian (cascade)
            $penggajian->delete();

            DB::commit();

            return redirect()
                ->route('penggajian.index')
                ->with('success', 'Data penggajian berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat menghapus data penggajian: ' . $e->getMessage());
        }
    }

    /**
     * Generate penggajian massal untuk semua karyawan aktif.
     */
    public function generateBulk(): View
    {
        return view('penggajian.generate-bulk');
    }

    /**
     * Process bulk generation of penggajian.
     */
    public function processBulkGenerate(): RedirectResponse
    {
        try {
            $periode = request('periode');
            
            if (!$periode) {
                return redirect()
                    ->back()
                    ->with('error', 'Periode harus diisi.');
            }

            DB::beginTransaction();

            // FR-28: Gunakan service untuk generate penggajian massal
            $result = $this->penggajianService->generateBulkPenggajian($periode);

            DB::commit();

            return redirect()
                ->route('penggajian.index', ['periode' => $periode])
                ->with('success', "Berhasil membuat {$result['created']} penggajian. Dilewati: {$result['skipped']} (sudah ada).");
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat generate penggajian massal: ' . $e->getMessage());
        }
    }

    /**
     * Update status penggajian (Draft -> Final -> Dibayar).
     */
    public function updateStatus(Penggajian $penggajian): RedirectResponse
    {
        try {
            $newStatus = request('status');
            
            if (!in_array($newStatus, ['draft', 'final', 'dibayar'])) {
                return redirect()
                    ->back()
                    ->with('error', 'Status tidak valid.');
            }

            DB::beginTransaction();

            // Gunakan service untuk update status dengan validasi
            $penggajian = $this->penggajianService->updateStatus($penggajian, $newStatus);

            DB::commit();

            return redirect()
                ->back()
                ->with('success', 'Status penggajian berhasil diubah menjadi ' . $newStatus . '.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat mengubah status: ' . $e->getMessage());
        }
    }
}

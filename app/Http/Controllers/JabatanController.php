<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use App\Http\Requests\StoreJabatanRequest;
use App\Http\Requests\UpdateJabatanRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class JabatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        // Eager loading untuk menghitung jumlah karyawan per jabatan
        $jabatans = Jabatan::withCount('karyawans')
            ->orderBy('nama_jabatan', 'asc')
            ->paginate(10);

        return view('jabatan.index', compact('jabatans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // Authorization sudah ditangani oleh middleware CheckRole
        return view('jabatan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreJabatanRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $jabatan = Jabatan::create($request->validated());

            DB::commit();

            return redirect()
                ->route('jabatan.index')
                ->with('success', 'Data jabatan berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data jabatan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Jabatan $jabatan): View
    {
        // Eager loading karyawan yang terkait dengan jabatan ini
        $jabatan->load(['karyawans' => function ($query) {
            $query->orderBy('nama_lengkap', 'asc');
        }]);

        return view('jabatan.show', compact('jabatan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Jabatan $jabatan): View
    {
        return view('jabatan.edit', compact('jabatan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateJabatanRequest $request, Jabatan $jabatan): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $jabatan->update($request->validated());

            DB::commit();

            return redirect()
                ->route('jabatan.index')
                ->with('success', 'Data jabatan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui data jabatan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Jabatan $jabatan): RedirectResponse
    {
        try {
            // BR-09: Validasi jabatan yang masih dipakai karyawan aktif tidak dapat dihapus
            $karyawanAktifCount = $jabatan->karyawans()
                ->where('status_karyawan', 'aktif')
                ->count();

            if ($karyawanAktifCount > 0) {
                return redirect()
                    ->back()
                    ->with('error', 'Jabatan tidak dapat dihapus karena masih digunakan oleh ' . $karyawanAktifCount . ' karyawan aktif.');
            }

            DB::beginTransaction();

            $jabatan->delete();

            DB::commit();

            return redirect()
                ->route('jabatan.index')
                ->with('success', 'Data jabatan berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat menghapus data jabatan: ' . $e->getMessage());
        }
    }
}

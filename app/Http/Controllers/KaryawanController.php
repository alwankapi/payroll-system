<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Jabatan;
use App\Models\User;
use App\Http\Requests\StoreKaryawanRequest;
use App\Http\Requests\UpdateKaryawanRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class KaryawanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        // Eager loading relasi user dan jabatan
        $karyawans = Karyawan::with(['user', 'jabatan'])
            ->when(request('status'), function ($query, $status) {
                return $query->where('status_karyawan', $status);
            })
            ->when(request('jabatan_id'), function ($query, $jabatanId) {
                return $query->where('jabatan_id', $jabatanId);
            })
            ->when(request('search'), function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('nama_lengkap', 'like', "%{$search}%")
                      ->orWhere('nik', 'like', "%{$search}%");
                });
            })
            ->orderBy('nama_lengkap', 'asc')
            ->paginate(15)
            ->withQueryString();

        // Data untuk filter
        $jabatans = Jabatan::orderBy('nama_jabatan', 'asc')->get();

        return view('karyawan.index', compact('karyawans', 'jabatans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $jabatans = Jabatan::orderBy('nama_jabatan', 'asc')->get();
        
        // Get users yang belum terhubung ke karyawan
        $availableUsers = User::whereDoesntHave('karyawan')
            ->where('role', 'karyawan')
            ->orderBy('name', 'asc')
            ->get();

        return view('karyawan.create', compact('jabatans', 'availableUsers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreKaryawanRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            // BR-11: Akun login terhubung ke satu data Karyawan
            $karyawan = Karyawan::create($request->validated());

            DB::commit();

            return redirect()
                ->route('karyawan.index')
                ->with('success', 'Data karyawan berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data karyawan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Karyawan $karyawan): View
    {
        // Eager loading relasi dengan riwayat penggajian
        $karyawan->load([
            'user',
            'jabatan',
            'penggajians' => function ($query) {
                $query->orderBy('periode', 'desc')->limit(12);
            }
        ]);

        return view('karyawan.show', compact('karyawan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Karyawan $karyawan): View
    {
        $jabatans = Jabatan::orderBy('nama_jabatan', 'asc')->get();
        
        // Get users yang belum terhubung ke karyawan (kecuali user karyawan ini)
        $availableUsers = User::where(function ($query) use ($karyawan) {
            $query->whereDoesntHave('karyawan')
                  ->orWhere('id', $karyawan->user_id);
        })
        ->where('role', 'karyawan')
        ->orderBy('name', 'asc')
        ->get();

        return view('karyawan.edit', compact('karyawan', 'jabatans', 'availableUsers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateKaryawanRequest $request, Karyawan $karyawan): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $karyawan->update($request->validated());

            DB::commit();

            return redirect()
                ->route('karyawan.index')
                ->with('success', 'Data karyawan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui data karyawan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Karyawan $karyawan): RedirectResponse
    {
        try {
            // Cek apakah karyawan memiliki riwayat penggajian
            $hasPenggajian = $karyawan->penggajians()->exists();

            if ($hasPenggajian) {
                return redirect()
                    ->back()
                    ->with('error', 'Karyawan tidak dapat dihapus karena memiliki riwayat penggajian. Ubah status menjadi nonaktif sebagai gantinya.');
            }

            DB::beginTransaction();

            // Hapus user terkait (cascade)
            $karyawan->delete();

            DB::commit();

            return redirect()
                ->route('karyawan.index')
                ->with('success', 'Data karyawan berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat menghapus data karyawan: ' . $e->getMessage());
        }
    }
}

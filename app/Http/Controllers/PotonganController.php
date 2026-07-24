<?php

namespace App\Http\Controllers;

use App\Models\Potongan;
use App\Http\Requests\StorePotonganRequest;
use App\Http\Requests\UpdatePotonganRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class PotonganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $potongans = Potongan::query()
            ->when(request('status'), function ($query, $status) {
                $isActive = $status === 'aktif';
                return $query->where('status_aktif', $isActive);
            })
            ->when(request('jenis'), function ($query, $jenis) {
                return $query->where('jenis_potongan', $jenis);
            })
            ->orderBy('nama_potongan', 'asc')
            ->paginate(15);

        return view('potongan.index', compact('potongans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('potongan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePotonganRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $potongan = Potongan::create($request->validated());

            DB::commit();

            return redirect()
                ->route('potongan.index')
                ->with('success', 'Data potongan berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data potongan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Potongan $potongan): View
    {
        // Eager loading riwayat penggunaan potongan
        $potongan->loadCount('penggajianDetails');

        return view('potongan.show', compact('potongan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Potongan $potongan): View
    {
        return view('potongan.edit', compact('potongan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePotonganRequest $request, Potongan $potongan): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $potongan->update($request->validated());

            DB::commit();

            return redirect()
                ->route('potongan.index')
                ->with('success', 'Data potongan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui data potongan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Potongan $potongan): RedirectResponse
    {
        try {
            // FR-20: Cek apakah potongan sudah pernah dipakai di penggajian
            $hasBeenUsed = $potongan->penggajianDetails()->exists();

            if ($hasBeenUsed) {
                return redirect()
                    ->back()
                    ->with('error', 'Potongan tidak dapat dihapus karena sudah pernah digunakan dalam penggajian. Nonaktifkan potongan sebagai gantinya.');
            }

            DB::beginTransaction();

            $potongan->delete();

            DB::commit();

            return redirect()
                ->route('potongan.index')
                ->with('success', 'Data potongan berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat menghapus data potongan: ' . $e->getMessage());
        }
    }
}

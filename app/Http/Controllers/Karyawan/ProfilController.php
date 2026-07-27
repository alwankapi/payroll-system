<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Karyawan\UpdatePasswordRequest;
use App\Http\Requests\Karyawan\UpdateProfilRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfilController extends Controller
{
    /**
     * Display profil karyawan.
     */
    public function show(Request $request)
    {
        $karyawan = $request->user()->karyawan;
        $karyawan->load('jabatan');

        return view('karyawan.profil.show', compact('karyawan'));
    }

    /**
     * Show form edit profil.
     */
    public function edit(Request $request)
    {
        $karyawan = $request->user()->karyawan;
        $karyawan->load('jabatan');

        return view('karyawan.profil.edit', compact('karyawan'));
    }

    /**
     * Update profil karyawan.
     */
    public function update(UpdateProfilRequest $request)
    {
        $karyawan = $request->user()->karyawan;
        
        $data = [
            'no_telepon' => $request->no_telepon,
            'alamat' => $request->alamat,
        ];

        // Handle foto upload
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($karyawan->foto && Storage::disk('public')->exists($karyawan->foto)) {
                Storage::disk('public')->delete($karyawan->foto);
            }

            // Upload foto baru
            $path = $request->file('foto')->store('karyawan/foto', 'public');
            $data['foto'] = $path;
        }

        $karyawan->update($data);

        return redirect()->route('karyawan.profil')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Show form ubah password.
     */
    public function editPassword()
    {
        return view('karyawan.profil.password');
    }

    /**
     * Update password karyawan.
     */
    public function updatePassword(UpdatePasswordRequest $request)
    {
        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('karyawan.profil')
            ->with('success', 'Password berhasil diubah.');
    }
}

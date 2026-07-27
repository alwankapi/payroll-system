<x-app-layout>
    <x-slot name="breadcrumbs">
        [
            ['label' => 'Dashboard', 'route' => 'karyawan.dashboard'],
            ['label' => 'Profil Saya', 'route' => 'karyawan.profil.index'],
            ['label' => 'Edit Profil']
        ]
    </x-slot>

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Edit Profil</h2>

            <form method="POST" action="{{ route('karyawan.profil.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Data yang tidak bisa diedit (readonly) -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white border-b pb-2">Data Tetap</h3>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Lengkap</label>
                            <input type="text" value="{{ $karyawan->nama_lengkap }}" disabled class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 shadow-sm">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Nama tidak dapat diubah</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">NIP</label>
                            <input type="text" value="{{ $karyawan->nik }}" disabled class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 shadow-sm">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">NIP tidak dapat diubah</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                            <input type="email" value="{{ $karyawan->user->email }}" disabled class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 shadow-sm">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Email tidak dapat diubah</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jabatan</label>
                            <input type="text" value="{{ $karyawan->jabatan->nama_jabatan }}" disabled class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 shadow-sm">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Jabatan tidak dapat diubah</p>
                        </div>
                    </div>

                    <!-- Data yang bisa diedit -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white border-b pb-2">Data yang Dapat Diubah</h3>
                        
                        <div>
                            <label for="no_telepon" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nomor Telepon <span class="text-red-500">*</span></label>
                            <input type="text" name="no_telepon" id="no_telepon" value="{{ old('no_telepon', $karyawan->no_telepon) }}" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('no_telepon')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="alamat" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Alamat <span class="text-red-500">*</span></label>
                            <textarea name="alamat" id="alamat" rows="4" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('alamat', $karyawan->alamat) }}</textarea>
                            @error('alamat')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="foto" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Foto Profil (Opsional)</label>
                            <input type="file" name="foto" id="foto" accept="image/jpeg,image/png,image/jpg" class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-md file:border-0
                                file:text-sm file:font-semibold
                                file:bg-indigo-50 file:text-indigo-700
                                hover:file:bg-indigo-100
                                dark:file:bg-indigo-900 dark:file:text-indigo-300">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Format: JPG, PNG, JPEG. Maksimal 2MB.</p>
                            @error('foto')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end space-x-3">
                    <a href="{{ route('karyawan.profil.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-500 transition">
                        Batal
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

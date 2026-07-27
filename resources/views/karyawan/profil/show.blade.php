<x-app-layout>
    <x-slot name="breadcrumbs">
        [
            ['label' => 'Dashboard', 'route' => 'karyawan.dashboard'],
            ['label' => 'Profil Saya']
        ]
    </x-slot>

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Profil Saya</h2>
                <a href="{{ route('karyawan.profil.edit') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit Profil
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Data Pribadi -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white border-b pb-2">Data Pribadi</h3>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Lengkap</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $karyawan->nama_lengkap }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">NIP</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $karyawan->nik }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $karyawan->user->email }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jabatan</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $karyawan->jabatan->nama_jabatan }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Masuk</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $karyawan->tanggal_masuk->format('d F Y') }}</p>
                    </div>
                </div>

                <!-- Data Kontak -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white border-b pb-2">Data Kontak</h3>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nomor Telepon</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $karyawan->no_telepon ?: '-' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Alamat</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $karyawan->alamat ?: '-' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">No. Rekening</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $karyawan->no_rekening ?: '-' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status Karyawan</label>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $karyawan->status_karyawan === 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($karyawan->status_karyawan) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-6 border-t">
                <a href="{{ route('karyawan.password.edit') }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Ubah Password
                </a>
            </div>
        </div>
    </div>
</x-app-layout>

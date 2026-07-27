<x-app-layout>
    <x-slot name="breadcrumbs">
        [
            ['label' => 'Dashboard', 'route' => 'karyawan.dashboard'],
            ['label' => 'Riwayat Gaji', 'route' => 'karyawan.riwayat-gaji.index'],
            ['label' => 'Detail Slip Gaji']
        ]
    </x-slot>

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Slip Gaji - {{ $penggajian->periode->format('F Y') }}
                </h2>
                @if(in_array($penggajian->status, ['final', 'dibayar']))
                <a href="{{ route('karyawan.riwayat-gaji.download', $penggajian) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Download PDF
                </a>
                @endif
            </div>

            <!-- Info Karyawan -->
            <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Nama</p>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $penggajian->karyawan->nama_lengkap }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">NIP</p>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $penggajian->karyawan->nik }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Jabatan</p>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $penggajian->karyawan->jabatan->nama_jabatan }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Status</p>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $penggajian->status_badge_class }}">
                            {{ $penggajian->status_label }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Detail Gaji -->
            <div class="space-y-4">
                <div class="flex justify-between py-2 border-b dark:border-gray-700">
                    <span class="text-gray-700 dark:text-gray-300">Gaji Pokok</span>
                    <span class="font-semibold text-gray-900 dark:text-white">Rp {{ number_format($penggajian->gaji_pokok, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-2 border-b dark:border-gray-700">
                    <span class="text-gray-700 dark:text-gray-300">Tunjangan</span>
                    <span class="font-semibold text-gray-900 dark:text-white">Rp {{ number_format($penggajian->tunjangan, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-2 border-b dark:border-gray-700">
                    <span class="text-red-600 dark:text-red-400">Potongan Alpha ({{ $penggajian->jumlah_alpha }} hari)</span>
                    <span class="font-semibold text-red-600 dark:text-red-400">- Rp {{ number_format($penggajian->potongan_alpha, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-3 bg-green-50 dark:bg-green-900 rounded-lg px-4 mt-4">
                    <span class="text-lg font-bold text-green-800 dark:text-green-200">Gaji Bersih</span>
                    <span class="text-lg font-bold text-green-800 dark:text-green-200">Rp {{ number_format($penggajian->gaji_bersih, 0, ',', '.') }}</span>
                </div>
            </div>

            @if($penggajian->catatan)
            <div class="mt-6 p-4 bg-yellow-50 dark:bg-yellow-900 rounded-lg">
                <p class="text-sm font-semibold text-yellow-800 dark:text-yellow-200 mb-1">Catatan:</p>
                <p class="text-sm text-yellow-700 dark:text-yellow-300">{{ $penggajian->catatan }}</p>
            </div>
            @endif

            <div class="mt-6">
                <a href="{{ route('karyawan.riwayat-gaji.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-500 transition">
                    Kembali
                </a>
            </div>
        </div>
    </div>
</x-app-layout>

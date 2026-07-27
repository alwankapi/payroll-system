<x-app-layout>
    <x-slot name="breadcrumbs">
        [
            ['label' => 'Jabatan', 'url' => '{{ route("jabatan.index") }}'],
            ['label' => 'Edit Jabatan']
        ]
    </x-slot>

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Edit Jabatan: {{ $jabatan->nama_jabatan }}</h2>
                <a href="{{ route('jabatan.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali
                </a>
            </div>

            <form action="{{ route('jabatan.update', $jabatan) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="nama_jabatan" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Nama Jabatan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama_jabatan" id="nama_jabatan" value="{{ old('nama_jabatan', $jabatan->nama_jabatan) }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm @error('nama_jabatan') border-red-500 @enderror"
                        placeholder="Contoh: Manager, Staff, Supervisor">
                    @error('nama_jabatan')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Nama jabatan harus unik dan maksimal 100 karakter</p>
                </div>

                <div>
                    <label for="gaji_pokok" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Gaji Pokok <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 dark:text-gray-400 sm:text-sm">Rp</span>
                        </div>
                        <input type="number" name="gaji_pokok" id="gaji_pokok" value="{{ old('gaji_pokok', $jabatan->gaji_pokok) }}" required min="0" step="1000"
                            class="block w-full pl-12 pr-12 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 @error('gaji_pokok') border-red-500 @enderror"
                            placeholder="5000000">
                    </div>
                    @error('gaji_pokok')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Minimal Rp 0, maksimal Rp 999.999.999</p>
                </div>

                <div>
                    <label for="tunjangan_jabatan" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Tunjangan Jabatan <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 dark:text-gray-400 sm:text-sm">Rp</span>
                        </div>
                        <input type="number" name="tunjangan_jabatan" id="tunjangan_jabatan" value="{{ old('tunjangan_jabatan', $jabatan->tunjangan_jabatan) }}" required min="0" step="1000"
                            class="block w-full pl-12 pr-12 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 @error('tunjangan_jabatan') border-red-500 @enderror"
                            placeholder="1000000">
                    </div>
                    @error('tunjangan_jabatan')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Minimal Rp 0, maksimal Rp 999.999.999. Kosongkan jika tidak ada tunjangan</p>
                </div>

                <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-md">
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ringkasan Gaji</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Gaji Pokok:</span>
                            <span id="display_gaji_pokok" class="font-medium text-gray-900 dark:text-white">Rp 0</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Tunjangan:</span>
                            <span id="display_tunjangan" class="font-medium text-gray-900 dark:text-white">Rp 0</span>
                        </div>
                        <div class="flex justify-between pt-2 border-t border-gray-200 dark:border-gray-700">
                            <span class="font-semibold text-gray-900 dark:text-white">Total Gaji:</span>
                            <span id="display_total_gaji" class="font-semibold text-indigo-600 dark:text-indigo-400">Rp 0</span>
                        </div>
                    </div>
                </div>

                @if($jabatan->karyawans()->count() > 0)
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Perhatian</h3>
                            <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                                <p>Jabatan ini digunakan oleh {{ $jabatan->karyawans()->count() }} karyawan. Perubahan gaji akan mempengaruhi perhitungan penggajian berikutnya.</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('jabatan.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                        Batal
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Update Jabatan
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function formatRupiah(angka) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka);
        }

        function updateRingkasan() {
            const gajiPokok = parseInt(document.getElementById('gaji_pokok').value) || 0;
            const tunjanganJabatan = parseInt(document.getElementById('tunjangan_jabatan').value) || 0;
            const totalGaji = gajiPokok + tunjanganJabatan;

            document.getElementById('display_gaji_pokok').textContent = formatRupiah(gajiPokok);
            document.getElementById('display_tunjangan').textContent = formatRupiah(tunjanganJabatan);
            document.getElementById('display_total_gaji').textContent = formatRupiah(totalGaji);
        }

        document.getElementById('gaji_pokok').addEventListener('input', updateRingkasan);
        document.getElementById('tunjangan_jabatan').addEventListener('input', updateRingkasan);

        // Initialize on page load
        updateRingkasan();
    </script>
    @endpush
</x-app-layout>

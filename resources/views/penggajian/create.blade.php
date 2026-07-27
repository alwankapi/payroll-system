<x-app-layout>
    <x-slot name="breadcrumbs">
        [
            ['label' => 'Penggajian', 'url' => '{{ route("penggajian.index") }}'],
            ['label' => 'Tambah Penggajian']
        ]
    </x-slot>

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Tambah Penggajian Baru</h2>
                <a href="{{ route('penggajian.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-500 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali
                </a>
            </div>

            <form action="{{ route('penggajian.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="karyawan_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Karyawan <span class="text-red-500">*</span></label>
                        <select name="karyawan_id" id="karyawan_id" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm @error('karyawan_id') border-red-500 @enderror">
                            <option value="">Pilih Karyawan</option>
                            @foreach($karyawans as $karyawan)
                        <option value="{{ $karyawan->id }}" 
                            data-gaji-pokok="{{ $karyawan->jabatan->gaji_pokok }}"
                            data-tunjangan="{{ $karyawan->jabatan->tunjangan_jabatan }}"
                            {{ old('karyawan_id') == $karyawan->id ? 'selected' : '' }}>
                            {{ $karyawan->nama_lengkap }} - {{ $karyawan->jabatan->nama_jabatan }}
                        </option>
                            @endforeach
                        </select>
                        @error('karyawan_id')<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="periode" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Periode <span class="text-red-500">*</span></label>
                        <input type="month" name="periode" id="periode" value="{{ old('periode', date('Y-m')) }}" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm @error('periode') border-red-500 @enderror">
                        @error('periode')<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="gaji_pokok" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Gaji Pokok <span class="text-red-500">*</span></label>
                        <input type="number" name="gaji_pokok" id="gaji_pokok" value="{{ old('gaji_pokok') }}" required min="0" step="1" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm @error('gaji_pokok') border-red-500 @enderror">
                        @error('gaji_pokok')<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="tunjangan" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tunjangan <span class="text-red-500">*</span></label>
                        <input type="number" name="tunjangan" id="tunjangan" value="{{ old('tunjangan', 0) }}" required min="0" step="1" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm @error('tunjangan') border-red-500 @enderror">
                        @error('tunjangan')<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Potongan</label>
                    <div class="space-y-2 border border-gray-300 dark:border-gray-700 rounded-md p-4" id="potongan-list">
                        @foreach($potongans as $potongan)
                            <label class="flex items-center">
                                <input type="checkbox" name="potongan_ids[]" value="{{ $potongan->id }}" 
                                    data-jenis="{{ $potongan->jenis_potongan }}"
                                    data-nilai="{{ $potongan->nilai }}"
                                    data-nama="{{ $potongan->nama_potongan }}"
                                    {{ is_array(old('potongan_ids')) && in_array($potongan->id, old('potongan_ids')) ? 'checked' : '' }}
                                    class="potongan-checkbox rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                    {{ $potongan->nama_potongan }} 
                                    @if($potongan->jenis_potongan === 'nominal')
                                        (Rp {{ number_format($potongan->nilai, 0, ',', '.') }})
                                    @else
                                        ({{ number_format($potongan->nilai, 2) }}%)
                                    @endif
                                </span>
                            </label>
                        @endforeach
                    </div>
                    @error('potongan_ids')<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>

                <!-- Hidden fields untuk total_potongan dan gaji_bersih -->
                <input type="hidden" name="total_potongan" id="total_potongan" value="{{ old('total_potongan', 0) }}">
                <input type="hidden" name="gaji_bersih" id="gaji_bersih" value="{{ old('gaji_bersih', 0) }}">

                <!-- Preview Perhitungan -->
                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 space-y-2">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Preview Perhitungan Gaji</h3>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Gaji Pokok:</span>
                        <span class="font-medium text-gray-900 dark:text-white" id="preview-gaji-pokok">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Tunjangan:</span>
                        <span class="font-medium text-gray-900 dark:text-white" id="preview-tunjangan">Rp 0</span>
                    </div>
                    <div class="border-t border-gray-200 dark:border-gray-700 my-2"></div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Total Potongan:</span>
                        <span class="font-medium text-red-600 dark:text-red-400" id="preview-total-potongan">Rp 0</span>
                    </div>
                    <div id="preview-potongan-details" class="pl-4 space-y-1 text-xs text-gray-500 dark:text-gray-400">
                        <!-- Detail potongan akan muncul di sini -->
                    </div>
                    <div class="border-t-2 border-gray-300 dark:border-gray-600 my-2"></div>
                    <div class="flex justify-between text-base font-bold">
                        <span class="text-gray-900 dark:text-white">Gaji Bersih:</span>
                        <span class="text-green-600 dark:text-green-400" id="preview-gaji-bersih">Rp 0</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status <span class="text-red-500">*</span></label>
                        <select name="status" id="status" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm @error('status') border-red-500 @enderror">
                            <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="final" {{ old('status') == 'final' ? 'selected' : '' }}>Final</option>
                            <option value="dibayar" {{ old('status') == 'dibayar' ? 'selected' : '' }}>Dibayar</option>
                        </select>
                        @error('status')<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Default: Draft - Data baru masih bisa diedit</p>
                    </div>

                    <div>
                        <label for="tanggal_bayar" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Bayar</label>
                        <input type="date" name="tanggal_bayar" id="tanggal_bayar" value="{{ old('tanggal_bayar') }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm @error('tanggal_bayar') border-red-500 @enderror">
                        @error('tanggal_bayar')<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Wajib diisi jika status Dibayar</p>
                    </div>
                </div>

                <div>
                    <label for="catatan" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Catatan</label>
                    <textarea name="catatan" id="catatan" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">{{ old('catatan') }}</textarea>
                </div>

                <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('penggajian.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-500 transition">Batal</a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Simpan Penggajian
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        // Format rupiah helper
        function formatRupiah(angka) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka);
        }

        // Calculate total potongan dan gaji bersih
        function calculateSalary() {
            const gajiPokok = parseFloat(document.getElementById('gaji_pokok').value) || 0;
            const tunjangan = parseFloat(document.getElementById('tunjangan').value) || 0;
            
            // Calculate potongan
            let totalPotongan = 0;
            let potonganDetails = [];
            
            document.querySelectorAll('.potongan-checkbox:checked').forEach(checkbox => {
                const jenis = checkbox.getAttribute('data-jenis');
                const nilai = parseFloat(checkbox.getAttribute('data-nilai'));
                const nama = checkbox.getAttribute('data-nama');
                
                let nilaiPotongan = 0;
                if (jenis === 'persentase') {
                    nilaiPotongan = (gajiPokok * nilai) / 100;
                } else {
                    nilaiPotongan = nilai;
                }
                
                totalPotongan += nilaiPotongan;
                potonganDetails.push({
                    nama: nama,
                    nilai: nilaiPotongan
                });
            });
            
            // Calculate gaji bersih
            const gajiBersih = Math.max(0, gajiPokok + tunjangan - totalPotongan);
            
            // Update hidden inputs
            document.getElementById('total_potongan').value = totalPotongan.toFixed(2);
            document.getElementById('gaji_bersih').value = gajiBersih.toFixed(2);
            
            // Update preview
            document.getElementById('preview-gaji-pokok').textContent = formatRupiah(gajiPokok);
            document.getElementById('preview-tunjangan').textContent = formatRupiah(tunjangan);
            document.getElementById('preview-total-potongan').textContent = formatRupiah(totalPotongan);
            document.getElementById('preview-gaji-bersih').textContent = formatRupiah(gajiBersih);
            
            // Update potongan details
            const detailsDiv = document.getElementById('preview-potongan-details');
            if (potonganDetails.length > 0) {
                detailsDiv.innerHTML = potonganDetails.map(p => 
                    `<div>• ${p.nama}: ${formatRupiah(p.nilai)}</div>`
                ).join('');
            } else {
                detailsDiv.innerHTML = '<div class="text-gray-400 italic">Tidak ada potongan</div>';
            }
        }

        // Auto-fill gaji pokok dan tunjangan saat memilih karyawan
        document.getElementById('karyawan_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const gajiPokok = selectedOption.getAttribute('data-gaji-pokok');
            const tunjangan = selectedOption.getAttribute('data-tunjangan');
            
            if (gajiPokok && tunjangan) {
                document.getElementById('gaji_pokok').value = gajiPokok;
                document.getElementById('tunjangan').value = tunjangan;
                calculateSalary();
            } else {
                // Reset jika tidak ada data
                document.getElementById('gaji_pokok').value = '';
                document.getElementById('tunjangan').value = '0';
                calculateSalary();
            }
        });

        // Recalculate saat gaji pokok atau tunjangan berubah
        document.getElementById('gaji_pokok').addEventListener('input', calculateSalary);
        document.getElementById('tunjangan').addEventListener('input', calculateSalary);

        // Recalculate saat potongan checkbox berubah
        document.querySelectorAll('.potongan-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', calculateSalary);
        });

        // Initial calculation
        calculateSalary();
    </script>
    @endpush
</x-app-layout>

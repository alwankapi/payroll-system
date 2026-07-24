<x-app-layout>
    <x-slot name="breadcrumbs">
        [
            ['label' => 'Potongan', 'url' => '{{ route("potongans.index") }}'],
            ['label' => 'Edit Potongan']
        ]
    </x-slot>

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Edit Potongan: {{ $potongan->nama_potongan }}</h2>
                <a href="{{ route('potongans.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-500 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali
                </a>
            </div>

            <form action="{{ route('potongans.update', $potongan) }}" method="POST" class="space-y-6" x-data="potonganForm()">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="nama_potongan" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Potongan <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_potongan" id="nama_potongan" value="{{ old('nama_potongan', $potongan->nama_potongan) }}" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm @error('nama_potongan') border-red-500 @enderror">
                        @error('nama_potongan')<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="jenis_potongan" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jenis Potongan <span class="text-red-500">*</span></label>
                        <select name="jenis_potongan" id="jenis_potongan" required x-model="jenis" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm @error('jenis_potongan') border-red-500 @enderror">
                            <option value="">Pilih Jenis</option>
                            <option value="nominal" {{ old('jenis_potongan', $potongan->jenis_potongan) === 'nominal' ? 'selected' : '' }}>Nominal (Rp)</option>
                            <option value="persentase" {{ old('jenis_potongan', $potongan->jenis_potongan) === 'persentase' ? 'selected' : '' }}>Persentase (%)</option>
                        </select>
                        @error('jenis_potongan')<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="nilai" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            <span x-show="jenis === 'nominal'">Nilai Nominal (Rp)</span>
                            <span x-show="jenis === 'persentase'">Nilai Persentase (%)</span>
                            <span x-show="!jenis">Nilai</span>
                            <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="nilai" id="nilai" value="{{ old('nilai', $potongan->nilai) }}" required step="0.01" min="0" x-on:input="calculatePreview" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm @error('nilai') border-red-500 @enderror">
                        @error('nilai')<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="deskripsi" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi</label>
                        <textarea name="deskripsi" id="deskripsi" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">{{ old('deskripsi', $potongan->deskripsi) }}</textarea>
                    </div>

                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="status_aktif" value="1" {{ old('status_aktif', $potongan->status_aktif) ? 'checked' : '' }} class="rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-indigo-600 focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Status Aktif</span>
                        </label>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Potongan aktif akan otomatis diterapkan pada penggajian baru</p>
                    </div>
                </div>

                <div x-show="jenis && nilai > 0" class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-md p-4">
                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-2">Preview Potongan</h3>
                    <div class="space-y-2 text-sm text-blue-700 dark:text-blue-300">
                        <p>Untuk gaji pokok Rp 5.000.000:</p>
                        <p class="font-semibold" x-text="previewText"></p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('potongans.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-500 transition">Batal</a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Update Potongan
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function potonganForm() {
            return {
                jenis: '{{ old("jenis_potongan", $potongan->jenis_potongan) }}',
                nilai: {{ old('nilai', $potongan->nilai) }},
                previewText: '',
                
                init() {
                    this.calculatePreview();
                },
                
                calculatePreview() {
                    const gajiPokok = 5000000;
                    const nilai = parseFloat(document.getElementById('nilai')?.value || 0);
                    
                    if (!this.jenis || nilai <= 0) {
                        this.previewText = '';
                        return;
                    }
                    
                    let potongan = 0;
                    if (this.jenis === 'nominal') {
                        potongan = nilai;
                    } else if (this.jenis === 'persentase') {
                        potongan = gajiPokok * (nilai / 100);
                    }
                    
                    const gajiBersih = gajiPokok - potongan;
                    this.previewText = `Potongan: Rp ${this.formatRupiah(potongan)} → Gaji Bersih: Rp ${this.formatRupiah(gajiBersih)}`;
                },
                
                formatRupiah(amount) {
                    return new Intl.NumberFormat('id-ID').format(amount);
                }
            }
        }
    </script>
    @endpush
</x-app-layout>

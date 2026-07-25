<x-app-layout>
    <x-slot name="breadcrumbs">
        [
            ['label' => 'Potongan', 'url' => '{{ route("potongan.index") }}'],
            ['label' => 'Detail Potongan']
        ]
    </x-slot>

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Detail Potongan</h2>
                <div class="flex gap-2">
                    <a href="{{ route('potongan.edit', $potongan) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit
                    </a>
                    <a href="{{ route('potongan.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-500 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Kembali
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Nama Potongan</h3>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $potongan->nama_potongan }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Jenis Potongan</h3>
                    @if($potongan->jenis_potongan === 'nominal')
                        <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">Nominal</span>
                    @else
                        <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300">Persentase</span>
                    @endif
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Nilai</h3>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">
                        @if($potongan->jenis_potongan === 'nominal')
                            Rp {{ number_format($potongan->nilai, 0, ',', '.') }}
                        @else
                            {{ number_format($potongan->nilai, 2) }}%
                        @endif
                    </p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Status</h3>
                    @if($potongan->status_aktif)
                        <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">Aktif</span>
                    @else
                        <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">Nonaktif</span>
                    @endif
                </div>

                @if($potongan->deskripsi)
                <div class="md:col-span-2">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Deskripsi</h3>
                    <p class="text-lg text-gray-900 dark:text-white">{{ $potongan->deskripsi }}</p>
                </div>
                @endif

                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Dibuat Pada</h3>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $potongan->created_at->format('d M Y, H:i') }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Terakhir Diubah</h3>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $potongan->updated_at->format('d M Y, H:i') }}</p>
                </div>
            </div>

            <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Preview Hasil Potongan</h3>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Gaji Pokok</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Potongan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Gaji Setelah Potongan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @php
                                $sampleGaji = [3000000, 5000000, 7000000, 10000000, 15000000];
                            @endphp
                            @foreach($sampleGaji as $gaji)
                                @php
                                    if ($potongan->jenis_potongan === 'nominal') {
                                        $nilaiPotongan = $potongan->nilai;
                                    } else {
                                        $nilaiPotongan = $gaji * ($potongan->nilai / 100);
                                    }
                                    $gajiBersih = $gaji - $nilaiPotongan;
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                        Rp {{ number_format($gaji, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 dark:text-red-400">
                                        - Rp {{ number_format($nilaiPotongan, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-white">
                                        Rp {{ number_format($gajiBersih, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                    * Preview di atas menunjukkan perhitungan potongan untuk beberapa contoh gaji pokok.
                </p>
            </div>
        </div>
    </div>
</x-app-layout>

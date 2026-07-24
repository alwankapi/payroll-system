<x-app-layout>
    <x-slot name="breadcrumbs">
        [
            ['label' => 'Karyawan', 'url' => '{{ route("karyawans.index") }}'],
            ['label' => 'Detail Karyawan']
        ]
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Detail Karyawan</h2>
                    <div class="flex gap-2">
                        <a href="{{ route('karyawans.edit', $karyawan) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Edit
                        </a>
                        <a href="{{ route('karyawans.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-500 transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            Kembali
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">NIK</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $karyawan->nik }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Nama Lengkap</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $karyawan->nama_lengkap }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Email</h3>
                        <p class="text-lg text-gray-900 dark:text-white">{{ $karyawan->email }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">No. Telepon</h3>
                        <p class="text-lg text-gray-900 dark:text-white">{{ $karyawan->no_telepon ?? '-' }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Jabatan</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $karyawan->jabatan->nama_jabatan }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Gaji: Rp {{ number_format($karyawan->jabatan->total_gaji, 0, ',', '.') }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Status</h3>
                        @if($karyawan->status_karyawan === 'aktif')
                            <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">Aktif</span>
                        @else
                            <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">Nonaktif</span>
                        @endif
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Tanggal Bergabung</h3>
                        <p class="text-lg text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($karyawan->tanggal_bergabung)->format('d M Y') }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Akun Login</h3>
                        <p class="text-lg text-gray-900 dark:text-white">{{ $karyawan->user->name }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $karyawan->user->email }}</p>
                    </div>

                    @if($karyawan->alamat)
                    <div class="md:col-span-2">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Alamat</h3>
                        <p class="text-lg text-gray-900 dark:text-white">{{ $karyawan->alamat }}</p>
                    </div>
                    @endif

                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Dibuat Pada</h3>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $karyawan->created_at->format('d M Y, H:i') }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Terakhir Diubah</h3>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $karyawan->updated_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        @if($karyawan->penggajians->count() > 0)
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Riwayat Penggajian (12 Bulan Terakhir)</h3>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Periode</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Gaji Pokok</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Tunjangan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Potongan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Gaji Bersih</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($karyawan->penggajians as $penggajian)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($penggajian->periode)->format('F Y') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                    Rp {{ number_format($penggajian->gaji_pokok, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                    Rp {{ number_format($penggajian->tunjangan, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                    Rp {{ number_format($penggajian->total_potongan, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-white">
                                    Rp {{ number_format($penggajian->gaji_bersih, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($penggajian->status === 'draft')
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">Draft</span>
                                    @elseif($penggajian->status === 'final')
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">Final</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">Dibayar</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <a href="{{ route('penggajians.show', $penggajian) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">Lihat Detail</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @else
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Belum Ada Riwayat Penggajian</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Karyawan ini belum memiliki riwayat penggajian.</p>
                </div>
            </div>
        </div>
        @endif
    </div>
</x-app-layout>

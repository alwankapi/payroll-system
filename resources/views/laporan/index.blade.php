<x-app-layout>
    <x-slot name="breadcrumbs">[['label' => 'Laporan Penggajian']]</x-slot>

    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Laporan Penggajian</h2>
            
            <form method="GET" class="mb-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                    <select name="bulan" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                        <option value="">Semua Bulan</option>
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$i,1)) }}</option>
                        @endfor
                    </select>
                    <select name="tahun" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                        <option value="">Semua Tahun</option>
                        @for($y = date('Y'); $y >= date('Y')-3; $y--)
                            <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                    <select name="jabatan_id" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                        <option value="">Semua Jabatan</option>
                        @foreach($jabatans as $jabatan)
                            <option value="{{ $jabatan->id }}" {{ request('jabatan_id') == $jabatan->id ? 'selected' : '' }}>{{ $jabatan->nama_jabatan }}</option>
                        @endforeach
                    </select>
                    <select name="status" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                        <option value="">Semua Status</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="diproses" {{ request('status') === 'diproses' ? 'selected' : '' }}>Diproses</option>
                        <option value="disetujui" {{ request('status') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                        <option value="dibayar" {{ request('status') === 'dibayar' ? 'selected' : '' }}>Dibayar</option>
                        <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        <option value="dibatalkan" {{ request('status') === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-800 rounded-md text-xs font-semibold uppercase">Filter</button>
                    @if(request()->hasAny(['bulan', 'tahun', 'jabatan_id', 'status']))
                        <a href="{{ route('laporan.index') }}" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md text-xs font-semibold uppercase">Reset</a>
                    @endif
                </div>
            </form>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg">
                    <div class="text-sm text-blue-600 dark:text-blue-300 mb-1">Total Gaji Bersih</div>
                    <div class="text-2xl font-bold text-blue-900 dark:text-blue-100">Rp {{ number_format($summary['total_gaji_bersih'], 0, ',', '.') }}</div>
                </div>
                <div class="bg-green-50 dark:bg-green-900 p-4 rounded-lg">
                    <div class="text-sm text-green-600 dark:text-green-300 mb-1">Total Tunjangan</div>
                    <div class="text-2xl font-bold text-green-900 dark:text-green-100">Rp {{ number_format($summary['total_tunjangan'], 0, ',', '.') }}</div>
                </div>
                <div class="bg-red-50 dark:bg-red-900 p-4 rounded-lg">
                    <div class="text-sm text-red-600 dark:text-red-300 mb-1">Total Potongan</div>
                    <div class="text-2xl font-bold text-red-900 dark:text-red-100">Rp {{ number_format($summary['total_potongan'], 0, ',', '.') }}</div>
                </div>
                <div class="bg-indigo-50 dark:bg-indigo-900 p-4 rounded-lg">
                    <div class="text-sm text-indigo-600 dark:text-indigo-300 mb-1">Jumlah Karyawan</div>
                    <div class="text-2xl font-bold text-indigo-900 dark:text-indigo-100">{{ $summary['jumlah_karyawan'] }} Orang</div>
                </div>
                <div class="bg-purple-50 dark:bg-purple-900 p-4 rounded-lg">
                    <div class="text-sm text-purple-600 dark:text-purple-300 mb-1">Total Transaksi</div>
                    <div class="text-2xl font-bold text-purple-900 dark:text-purple-100">{{ $summary['jumlah_transaksi'] }} Data</div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg flex flex-col gap-2">
                    <form method="GET" action="{{ route('laporan.exportPdf') }}" class="flex-1">
                        <input type="hidden" name="bulan" value="{{ request('bulan') }}">
                        <input type="hidden" name="tahun" value="{{ request('tahun') }}">
                        <input type="hidden" name="jabatan_id" value="{{ request('jabatan_id') }}">
                        <input type="hidden" name="status" value="{{ request('status') }}">
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-md text-xs font-semibold uppercase hover:bg-red-700">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Export PDF
                        </button>
                    </form>
                    <form method="GET" action="{{ route('laporan.exportExcel') }}" class="flex-1">
                        <input type="hidden" name="bulan" value="{{ request('bulan') }}">
                        <input type="hidden" name="tahun" value="{{ request('tahun') }}">
                        <input type="hidden" name="jabatan_id" value="{{ request('jabatan_id') }}">
                        <input type="hidden" name="status" value="{{ request('status') }}">
                        <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-md text-xs font-semibold uppercase hover:bg-green-700">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Export Excel
                        </button>
                    </form>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Karyawan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Periode</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Gaji Pokok</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Tunjangan</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Potongan</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Gaji Bersih</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($penggajians as $penggajian)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $penggajian->karyawan->nama_lengkap }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $penggajian->karyawan->jabatan->nama_jabatan }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                {{ \Carbon\Carbon::parse($penggajian->periode)->format('F Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900 dark:text-gray-300">
                                Rp {{ number_format($penggajian->gaji_pokok, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-green-600 dark:text-green-400">
                                Rp {{ number_format($penggajian->tunjangan, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-red-600 dark:text-red-400">
                                Rp {{ number_format($penggajian->total_potongan, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-gray-900 dark:text-white">
                                Rp {{ number_format($penggajian->gaji_bersih, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $penggajian->status_badge_class }}">
                                    {{ $penggajian->status_label }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                Tidak ada data laporan sesuai filter.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

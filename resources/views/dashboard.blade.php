<x-app-layout>
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Karyawan</dt>
                            <dd class="text-lg font-semibold text-gray-900 dark:text-white">{{ $totalKaryawan }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Jabatan</dt>
                            <dd class="text-lg font-semibold text-gray-900 dark:text-white">{{ $totalJabatan }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Penggajian</dt>
                            <dd class="text-lg font-semibold text-gray-900 dark:text-white">{{ $totalPenggajian }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Gaji Bulan Ini</dt>
                            <dd class="text-lg font-semibold text-gray-900 dark:text-white">Rp {{ number_format($totalGajiBulanIni, 0, ',', '.') }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-red-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Potongan Bulan Ini</dt>
                            <dd class="text-lg font-semibold text-gray-900 dark:text-white">Rp {{ number_format($totalPotonganBulanIni, 0, ',', '.') }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Line Chart: Penggajian Bulanan -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Penggajian Bulanan (6 Bulan Terakhir)</h3>
            <canvas id="penggajianChart" class="w-full" height="250"></canvas>
        </div>

        <!-- Pie Chart: Status Penggajian -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Status Penggajian</h3>
            <canvas id="statusChart" class="w-full" height="250"></canvas>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <a href="{{ route('karyawan.create') }}" class="block p-6 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-lg hover:from-blue-600 hover:to-blue-700 transition">
            <div class="flex items-center text-white">
                <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                <div>
                    <div class="text-sm opacity-90">Tambah</div>
                    <div class="text-lg font-bold">Karyawan Baru</div>
                </div>
            </div>
        </a>

        <a href="{{ route('penggajian.generateBulk') }}" class="block p-6 bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow-lg hover:from-green-600 hover:to-green-700 transition">
            <div class="flex items-center text-white">
                <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <div>
                    <div class="text-sm opacity-90">Generate</div>
                    <div class="text-lg font-bold">Gaji Bulk</div>
                </div>
            </div>
        </a>

        <a href="{{ route('laporan.index') }}" class="block p-6 bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg shadow-lg hover:from-purple-600 hover:to-purple-700 transition">
            <div class="flex items-center text-white">
                <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <div>
                    <div class="text-sm opacity-90">Lihat</div>
                    <div class="text-lg font-bold">Laporan</div>
                </div>
            </div>
        </a>

        <a href="{{ route('penggajian.index') }}" class="block p-6 bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg shadow-lg hover:from-orange-600 hover:to-orange-700 transition">
            <div class="flex items-center text-white">
                <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <div>
                    <div class="text-sm opacity-90">Kelola</div>
                    <div class="text-lg font-bold">Penggajian</div>
                </div>
            </div>
        </a>
    </div>

    <!-- Top 5 & Activities Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Top 5 Jabatan -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Top 5 Jabatan (Gaji Terbesar)</h3>
            <div class="space-y-3">
                @forelse($topJabatan as $jabatan)
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $jabatan->nama_jabatan }}</span>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">Rp {{ number_format($jabatan->total_gaji, 0, ',', '.') }}</span>
                </div>
                @empty
                <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">Belum ada data</p>
                @endforelse
            </div>
        </div>

        <!-- Top 5 Potongan -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Top 5 Potongan (Aktif)</h3>
            <div class="space-y-3">
                @forelse($topPotongan as $potongan)
                <div class="flex justify-between items-center">
                    <div class="flex-1">
                        <div class="text-sm text-gray-700 dark:text-gray-300">{{ $potongan->nama_potongan }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ ucfirst($potongan->jenis_potongan) }}</div>
                    </div>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">
                        @if($potongan->jenis_potongan === 'nominal')
                            Rp {{ number_format($potongan->nilai, 0, ',', '.') }}
                        @else
                            {{ $potongan->nilai }}%
                        @endif
                    </span>
                </div>
                @empty
                <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">Belum ada data</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Aktivitas Terbaru</h3>
            <div class="space-y-3">
                @forelse($recentActivities as $activity)
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        @if($activity['type'] === 'penggajian')
                            <div class="w-2 h-2 bg-blue-500 rounded-full mt-1.5"></div>
                        @else
                            <div class="w-2 h-2 bg-green-500 rounded-full mt-1.5"></div>
                        @endif
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $activity['description'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $activity['time']->diffForHumans() }}</p>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">Belum ada aktivitas</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Chart.js CDN & Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // Line Chart: Penggajian Bulanan
        const penggajianCtx = document.getElementById('penggajianChart');
        if (penggajianCtx) {
            new Chart(penggajianCtx, {
                type: 'line',
                data: {
                    labels: {composer require maatwebsite/excel json_encode($chartData->pluck('month')) !!},
                    datasets: [{
                        label: 'Total Gaji Bersih',
                        data: {composer require maatwebsite/excel json_encode($chartData->pluck('total')) !!},
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            labels: {
                                color: document.documentElement.classList.contains('dark') ? '#fff' : '#000'
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                                },
                                color: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#6b7280'
                            },
                            grid: {
                                color: document.documentElement.classList.contains('dark') ? '#374151' : '#e5e7eb'
                            }
                        },
                        x: {
                            ticks: {
                                color: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#6b7280'
                            },
                            grid: {
                                color: document.documentElement.classList.contains('dark') ? '#374151' : '#e5e7eb'
                            }
                        }
                    }
                }
            });
        }

        // Pie Chart: Status Penggajian
        const statusCtx = document.getElementById('statusChart');
        if (statusCtx) {
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Draft', 'Final', 'Dibayar'],
                    datasets: [{
                        data: [
                            {{ $statusStats['draft'] }},
                            {{ $statusStats['final'] }},
                            {{ $statusStats['dibayar'] }}
                        ],
                        backgroundColor: [
                            'rgb(107, 114, 128)',
                            'rgb(59, 130, 246)',
                            'rgb(34, 197, 94)'
                        ],
                        borderColor: [
                            'rgb(107, 114, 128)',
                            'rgb(59, 130, 246)',
                            'rgb(34, 197, 94)'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: document.documentElement.classList.contains('dark') ? '#fff' : '#000',
                                padding: 15
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((context.parsed / total) * 100).toFixed(1);
                                    return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });
        }
    </script>
</x-app-layout>

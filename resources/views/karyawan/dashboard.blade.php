<x-app-layout>
    <x-slot name="breadcrumbs">
        [['label' => 'Dashboard Karyawan']]
    </x-slot>

    <div class="space-y-6">
        <!-- Welcome Card -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">
                            Selamat Datang, {{ $karyawan->nama_lengkap }}!
                        </h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ $karyawan->jabatan->nama_jabatan }} - NIP: {{ $karyawan->nik }}
                        </p>
                    </div>
                    <div class="hidden sm:block">
                        <svg class="w-16 h-16 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Gaji Bulan Ini -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    Total Gaji Bulan Ini
                                </dt>
                                <dd class="text-lg font-semibold text-gray-900 dark:text-white">
                                    Rp {{ number_format($stats['total_gaji_bulan_ini'], 0, ',', '.') }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Potongan Bulan Ini -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-red-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    Potongan Bulan Ini
                                </dt>
                                <dd class="text-lg font-semibold text-red-600 dark:text-red-400">
                                    Rp {{ number_format($stats['total_potongan_bulan_ini'], 0, ',', '.') }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gaji Bersih Bulan Ini -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    Gaji Bersih Bulan Ini
                                </dt>
                                <dd class="text-lg font-semibold text-green-600 dark:text-green-400">
                                    Rp {{ number_format($stats['gaji_bersih_bulan_ini'], 0, ',', '.') }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Histori -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    Total Histori Gaji
                                </dt>
                                <dd class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ $stats['jumlah_histori'] }} Bulan
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Latest Penggajian Info -->
        @if($latestPenggajian)
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    Gaji Terakhir - {{ $latestPenggajian->periode->format('F Y') }}
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Gaji Pokok</p>
                        <p class="text-xl font-semibold text-gray-900 dark:text-white">
                            Rp {{ number_format($latestPenggajian->gaji_pokok, 0, ',', '.') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Tunjangan</p>
                        <p class="text-xl font-semibold text-gray-900 dark:text-white">
                            Rp {{ number_format($latestPenggajian->tunjangan, 0, ',', '.') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Status</p>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $latestPenggajian->status_badge_class }}">
                            {{ $latestPenggajian->status_label }}
                        </span>
                    </div>
                </div>
                <div class="mt-4 flex justify-end">
                    <a href="{{ route('karyawan.riwayat-gaji.show', $latestPenggajian) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                        Lihat Detail
                    </a>
                </div>
            </div>
        </div>
        @endif

        <!-- Quick Links -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="{{ route('karyawan.profil.index') }}" class="block bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition">
                <div class="p-6">
                    <div class="flex items-center">
                        <svg class="h-8 w-8 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <div class="ml-4">
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">Profil Saya</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Lihat dan edit profil</p>
                        </div>
                    </div>
                </div>
            </a>

            <a href="{{ route('karyawan.riwayat-gaji.index') }}" class="block bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition">
                <div class="p-6">
                    <div class="flex items-center">
                        <svg class="h-8 w-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <div class="ml-4">
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">Riwayat Gaji</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Lihat histori penggajian</p>
                        </div>
                    </div>
                </div>
            </a>

            <a href="{{ route('karyawan.password.edit') }}" class="block bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition">
                <div class="p-6">
                    <div class="flex items-center">
                        <svg class="h-8 w-8 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <div class="ml-4">
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">Ubah Password</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Ganti password akun</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</x-app-layout>

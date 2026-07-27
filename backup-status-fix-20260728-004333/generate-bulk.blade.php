<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Generate Penggajian Massal') }}
            </h2>
            <a href="{{ route('penggajian.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('penggajian.processBulkGenerate') }}">
                        @csrf
                        
                        <div class="mb-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                Fitur ini akan membuat data penggajian (draft) untuk semua karyawan pada periode yang dipilih.
                                Karyawan yang sudah memiliki data penggajian pada periode tersebut akan dilewati.
                            </p>
                        </div>

                        <!-- Periode -->
                        <div class="mb-4">
                            <x-input-label for="periode" :value="__('Periode (Bulan/Tahun)')" />
                            <x-text-input id="periode" class="block mt-1 w-full max-w-md" type="month" name="periode" :value="old('periode', now()->format('Y-m'))" required autofocus />
                            <x-input-error :messages="$errors->get('periode')" class="mt-2" />
                        </div>

                        <div class="flex items-center mt-4">
                            <x-primary-button onclick="return confirm('Apakah Anda yakin ingin men-generate data penggajian massal untuk periode ini? Proses ini mungkin membutuhkan waktu.')">
                                {{ __('Generate Penggajian') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
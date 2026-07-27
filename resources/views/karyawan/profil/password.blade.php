<x-app-layout>
    <x-slot name="breadcrumbs">
        [
            ['label' => 'Dashboard', 'route' => 'karyawan.dashboard'],
            ['label' => 'Ubah Password']
        ]
    </x-slot>

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Ubah Password</h2>

            <form method="POST" action="{{ route('karyawan.password.update') }}">
                @csrf
                @method('PUT')

                <div class="max-w-xl space-y-6">
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Password Lama <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="current_password" id="current_password" required 
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('current_password')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Password Baru <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="password" id="password" required 
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Minimal 8 karakter</p>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Konfirmasi Password Baru <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required 
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Ketik ulang password baru</p>
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-4">
                        <a href="{{ route('karyawan.profil.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-500 transition">
                            Batal
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            Ubah Password
                        </button>
                    </div>
                </div>
            </form>

            <div class="mt-8 p-4 bg-blue-50 dark:bg-blue-900 rounded-lg">
                <h3 class="text-sm font-semibold text-blue-800 dark:text-blue-200 mb-2">Tips Keamanan Password:</h3>
                <ul class="text-sm text-blue-700 dark:text-blue-300 list-disc list-inside space-y-1">
                    <li>Gunakan kombinasi huruf besar, huruf kecil, angka, dan simbol</li>
                    <li>Minimal 8 karakter (lebih panjang lebih baik)</li>
                    <li>Jangan gunakan informasi pribadi yang mudah ditebak</li>
                    <li>Ganti password secara berkala</li>
                    <li>Jangan bagikan password kepada siapapun</li>
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>

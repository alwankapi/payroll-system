<footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
    <div class="p-4 lg:px-6 lg:py-4">
        <div class="flex items-center justify-between">
            <span class="text-sm text-gray-500 dark:text-gray-400">
                © {{ date('Y') }} <a href="{{ route('dashboard') }}" class="hover:underline">Payroll System</a>. All rights reserved.
            </span>
            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-500 dark:text-gray-400">
                    Built with Laravel {{ app()->version() }}
                </span>
            </div>
        </div>
    </div>
</footer>

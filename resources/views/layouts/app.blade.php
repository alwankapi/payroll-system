<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
    x-data="{ 
        darkMode: localStorage.getItem('darkMode') === 'true',
        sidebarOpen: false 
    }" 
    x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" 
    :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Dashboard')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900">
    <div class="antialiased">
        
        <!-- Navbar -->
        @include('layouts.partials.navbar')
        
        <!-- Sidebar -->
        @include('layouts.partials.sidebar')

        <!-- Sidebar backdrop (mobile) -->
        <div x-show="sidebarOpen" 
            @click="sidebarOpen = false"
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-30 bg-gray-900/50 lg:hidden"
            style="display: none;">
        </div>

        <!-- Main Content -->
        <main class="p-4 lg:ml-64 h-auto pt-20">
            
            <!-- Breadcrumb -->
            @if(isset($breadcrumbs))
                <x-breadcrumb :items="$breadcrumbs" />
            @endif
            
            <!-- Flash Messages -->
            @include('layouts.partials.flash-message')
            
            <!-- Page Content -->
            <div class="mb-4">
                @if(isset($header))
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-4">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            {{ $header }}
                        </div>
                    </div>
                @endif
                
                {{ $slot }}
            </div>
            
            <!-- Footer -->
            @include('layouts.partials.footer')
        </main>
        
    </div>
    
    @stack('scripts')
</body>
</html>

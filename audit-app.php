<?php
// Comprehensive Audit Script untuk Sistem Penggajian

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "========================================\n";
echo "COMPREHENSIVE AUDIT - SISTEM PENGGAJIAN\n";
echo "========================================\n\n";

$issues = [];
$passed = [];

// TEST 1: Database Connection
echo "1. Test Database Connection...\n";
try {
    DB::connection()->getPdo();
    $passed[] = "✓ Database connection OK";
} catch (Exception $e) {
    $issues[] = "✗ Database connection FAILED: " . $e->getMessage();
}

// TEST 2: Check Tables Exist
echo "2. Check Required Tables...\n";
$tables = ['users', 'jabatans', 'karyawans', 'potongans', 'penggajians', 'penggajian_detail'];
foreach ($tables as $table) {
    try {
        DB::table($table)->count();
        $passed[] = "✓ Table $table exists";
    } catch (Exception $e) {
        $issues[] = "✗ Table $table MISSING or ERROR";
    }
}

// TEST 3: Check Data Availability
echo "\n3. Check Data Availability...\n";
$userCount = DB::table('users')->count();
$jabatanCount = DB::table('jabatans')->count();
$karyawanCount = DB::table('karyawans')->count();
$potonganCount = DB::table('potongans')->count();
$penggajianCount = DB::table('penggajians')->count();

echo "   Users: $userCount\n";
echo "   Jabatan: $jabatanCount\n";
echo "   Karyawan: $karyawanCount\n";
echo "   Potongan: $potonganCount\n";
echo "   Penggajian: $penggajianCount\n";

if ($userCount > 0) $passed[] = "✓ Users data available ($userCount records)";
else $issues[] = "✗ No users in database";

if ($jabatanCount > 0) $passed[] = "✓ Jabatan data available ($jabatanCount records)";
else $issues[] = "✗ No jabatan in database";

if ($karyawanCount > 0) $passed[] = "✓ Karyawan data available ($karyawanCount records)";
else $issues[] = "✗ No karyawan in database";

// TEST 4: Test PenggajianService
echo "\n4. Test PenggajianService...\n";
try {
    $service = app(\App\Services\PenggajianService::class);
    $karyawan = \App\Models\Karyawan::with('jabatan')->first();
    
    if (!$karyawan) {
        $issues[] = "✗ No karyawan found for testing";
    } else {
        $salaryData = $service->calculateSalary($karyawan, '2026-08-01');
        $passed[] = "✓ PenggajianService::calculateSalary works";
        
        // Validate calculation
        $expected = $salaryData['gaji_pokok'] + $salaryData['tunjangan'] - $salaryData['total_potongan'];
        if ($salaryData['gaji_bersih'] == $expected) {
            $passed[] = "✓ Salary calculation is correct";
        } else {
            $issues[] = "✗ Salary calculation WRONG: {$salaryData['gaji_bersih']} != $expected";
        }
    }
} catch (Exception $e) {
    $issues[] = "✗ PenggajianService ERROR: " . $e->getMessage();
}

// TEST 5: Test Potongan Calculation
echo "\n5. Test Potongan Calculation...\n";
try {
    $potongan = \App\Models\Potongan::first();
    if ($potongan) {
        $gajiPokok = 5000000;
        $hasil = $potongan->hitungPotongan($gajiPokok);
        
        if ($potongan->jenis_potongan === 'persentase') {
            $expected = $gajiPokok * $potongan->nilai / 100;
            if ($hasil == $expected) {
                $passed[] = "✓ Potongan persentase calculation correct";
            } else {
                $issues[] = "✗ Potongan persentase WRONG: $hasil != $expected";
            }
        } else {
            if ($hasil == $potongan->nilai) {
                $passed[] = "✓ Potongan nominal calculation correct";
            } else {
                $issues[] = "✗ Potongan nominal WRONG";
            }
        }
    }
} catch (Exception $e) {
    $issues[] = "✗ Potongan calculation ERROR: " . $e->getMessage();
}

// TEST 6: Test Controllers Exist
echo "\n6. Check Controllers...\n";
$controllers = [
    'DashboardController',
    'JabatanController',
    'KaryawanController',
    'PotonganController',
    'PenggajianController',
    'SlipGajiController',
    'LaporanController'
];

foreach ($controllers as $controller) {
    $class = "App\\Http\\Controllers\\$controller";
    if (class_exists($class)) {
        $passed[] = "✓ $controller exists";
    } else {
        $issues[] = "✗ $controller MISSING";
    }
}

// TEST 7: Test Views Exist
echo "\n7. Check Critical Views...\n";
$views = [
    'dashboard',
    'jabatan.index',
    'karyawan.index',
    'potongan.index',
    'penggajian.index',
    'penggajian.generate-bulk',
    'laporan.index',
    'pdf.slip-gaji'
];

foreach ($views as $view) {
    if (view()->exists($view)) {
        $passed[] = "✓ View $view exists";
    } else {
        $issues[] = "✗ View $view MISSING";
    }
}

// TEST 8: Test PDF Generation
echo "\n8. Test PDF Generation Capability...\n";
try {
    if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
        $passed[] = "✓ DomPDF library available";
    } else {
        $issues[] = "✗ DomPDF library MISSING";
    }
} catch (Exception $e) {
    $issues[] = "✗ PDF library ERROR";
}

// TEST 9: Test Excel Export
echo "\n9. Test Excel Export Capability...\n";
try {
    if (class_exists(\Maatwebsite\Excel\Facades\Excel::class)) {
        $passed[] = "✓ Laravel Excel library available";
    } else {
        $issues[] = "✗ Laravel Excel library MISSING";
    }
} catch (Exception $e) {
    $issues[] = "✗ Excel library ERROR";
}

// TEST 10: Test Middleware
echo "\n10. Check Middleware...\n";
try {
    if (class_exists(\App\Http\Middleware\CheckRole::class)) {
        $passed[] = "✓ CheckRole middleware exists";
    } else {
        $issues[] = "✗ CheckRole middleware MISSING";
    }
} catch (Exception $e) {
    $issues[] = "✗ Middleware check ERROR";
}

// SUMMARY
echo "\n========================================\n";
echo "AUDIT SUMMARY\n";
echo "========================================\n\n";

echo "✓ PASSED (" . count($passed) . " items):\n";
foreach ($passed as $item) {
    echo "  $item\n";
}

echo "\n";

if (count($issues) > 0) {
    echo "✗ ISSUES FOUND (" . count($issues) . " items):\n";
    foreach ($issues as $item) {
        echo "  $item\n";
    }
    echo "\n⚠️  DEMO READINESS: NEEDS ATTENTION\n";
    exit(1);
} else {
    echo "🎉 NO ISSUES FOUND!\n";
    echo "✅ DEMO READINESS: READY\n";
    exit(0);
}

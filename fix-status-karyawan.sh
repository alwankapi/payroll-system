#!/bin/bash

# =============================================================================
# SCRIPT AUTO-FIX STATUS KARYAWAN
# Mengubah semua referensi 'aktif/nonaktif' menjadi 'tetap/kontrak/magang'
# =============================================================================

echo "🔧 Starting auto-fix for Karyawan status..."
echo ""

# Backup notification
echo "📋 Creating backup..."
BACKUP_DIR="backup-status-fix-$(date +%Y%m%d-%H%M%S)"
mkdir -p "$BACKUP_DIR"

# Backup files that will be modified
cp resources/views/karyawan/edit.blade.php "$BACKUP_DIR/" 2>/dev/null
cp resources/views/karyawan/index.blade.php "$BACKUP_DIR/" 2>/dev/null
cp resources/views/karyawan/show.blade.php "$BACKUP_DIR/" 2>/dev/null
cp resources/views/karyawan/profil/show.blade.php "$BACKUP_DIR/" 2>/dev/null
cp resources/views/jabatan/show.blade.php "$BACKUP_DIR/" 2>/dev/null
cp resources/views/penggajian/generate-bulk.blade.php "$BACKUP_DIR/" 2>/dev/null
cp database/seeders/KaryawanSeeder.php "$BACKUP_DIR/" 2>/dev/null
cp tests/Feature/KaryawanTest.php "$BACKUP_DIR/" 2>/dev/null
cp tests/Unit/PenggajianServiceTest.php "$BACKUP_DIR/" 2>/dev/null

echo "✅ Backup created in $BACKUP_DIR"
echo ""

# =============================================================================
# FIX 1: resources/views/karyawan/edit.blade.php
# =============================================================================
echo "🔨 Fixing karyawan/edit.blade.php..."
sed -i "s/<option value=\"aktif\" {{ old('status_karyawan', \$karyawan->status_karyawan) === 'aktif' ? 'selected' : '' }}>Aktif<\/option>/<option value=\"tetap\" {{ old('status_karyawan', \$karyawan->status_karyawan) === 'tetap' ? 'selected' : '' }}>Tetap<\/option>/g" resources/views/karyawan/edit.blade.php
sed -i "s/<option value=\"nonaktif\" {{ old('status_karyawan', \$karyawan->status_karyawan) === 'nonaktif' ? 'selected' : '' }}>Nonaktif<\/option>/<option value=\"kontrak\" {{ old('status_karyawan', \$karyawan->status_karyawan) === 'kontrak' ? 'selected' : '' }}>Kontrak<\/option>\n                            <option value=\"magang\" {{ old('status_karyawan', \$karyawan->status_karyawan) === 'magang' ? 'selected' : '' }}>Magang<\/option>/g" resources/views/karyawan/edit.blade.php

# =============================================================================
# FIX 2: resources/views/karyawan/index.blade.php  
# =============================================================================
echo "🔨 Fixing karyawan/index.blade.php..."
sed -i "s/<option value=\"aktif\" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif<\/option>/<option value=\"tetap\" {{ request('status') == 'tetap' ? 'selected' : '' }}>Tetap<\/option>/g" resources/views/karyawan/index.blade.php
sed -i "s/<option value=\"nonaktif\" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif<\/option>/<option value=\"kontrak\" {{ request('status') == 'kontrak' ? 'selected' : '' }}>Kontrak<\/option>\n                                    <option value=\"magang\" {{ request('status') == 'magang' ? 'selected' : '' }}>Magang<\/option>/g" resources/views/karyawan/index.blade.php

# Fix badge display
sed -i "s/@if(\$karyawan->status_karyawan === 'aktif')/@if(\$karyawan->status_karyawan === 'tetap')/g" resources/views/karyawan/index.blade.php
sed -i "s/<span class=\"px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300\">Aktif<\/span>/<span class=\"{{ \$karyawan->statusBadgeClass() }}\">{{ \$karyawan->statusLabel() }}<\/span>/g" resources/views/karyawan/index.blade.php
sed -i "s/<span class=\"px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300\">Nonaktif<\/span>//g" resources/views/karyawan/index.blade.php

# =============================================================================
# FIX 3: resources/views/karyawan/show.blade.php
# =============================================================================
echo "🔨 Fixing karyawan/show.blade.php..."
sed -i "s/@if(\$karyawan->status_karyawan === 'aktif')/@if(in_array(\$karyawan->status_karyawan, ['tetap', 'kontrak', 'magang']))/g" resources/views/karyawan/show.blade.php
sed -i "s/<span class=\"px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300\">Aktif<\/span>/<span class=\"px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ \$karyawan->statusBadgeClass() }}\">{{ \$karyawan->statusLabel() }}<\/span>/g" resources/views/karyawan/show.blade.php  
sed -i "s/<span class=\"px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300\">Nonaktif<\/span>//g" resources/views/karyawan/show.blade.php

# =============================================================================
# FIX 4: resources/views/karyawan/profil/show.blade.php
# =============================================================================
echo "🔨 Fixing karyawan/profil/show.blade.php..."
sed -i "s/@if(\$karyawan->status_karyawan === 'aktif')/@if(in_array(\$karyawan->status_karyawan, ['tetap', 'kontrak', 'magang']))/g" resources/views/karyawan/profil/show.blade.php
sed -i "s/<span class=\"inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300\">Aktif<\/span>/<span class=\"inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ \$karyawan->statusBadgeClass() }}\">{{ \$karyawan->statusLabel() }}<\/span>/g" resources/views/karyawan/profil/show.blade.php
sed -i "s/<span class=\"inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300\">Nonaktif<\/span>//g" resources/views/karyawan/profil/show.blade.php

# =============================================================================
# FIX 5: resources/views/jabatan/show.blade.php
# =============================================================================
echo "🔨 Fixing jabatan/show.blade.php..."
sed -i "s/@if(\$karyawan->status === 'aktif')/@if(in_array(\$karyawan->status_karyawan, ['tetap', 'kontrak', 'magang']))/g" resources/views/jabatan/show.blade.php
sed -i "s/<span class=\"px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800\">Aktif<\/span>/<span class=\"px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ \$karyawan->statusBadgeClass() }}\">{{ \$karyawan->statusLabel() }}<\/span>/g" resources/views/jabatan/show.blade.php
sed -i "s/<span class=\"px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800\">Nonaktif<\/span>//g" resources/views/jabatan/show.blade.php

# =============================================================================
# FIX 6: resources/views/penggajian/generate-bulk.blade.php
# =============================================================================
echo "🔨 Fixing penggajian/generate-bulk.blade.php..."
sed -i "s/karyawan aktif/karyawan/g" resources/views/penggajian/generate-bulk.blade.php

# =============================================================================
# FIX 7: database/seeders/KaryawanSeeder.php
# =============================================================================
echo "🔨 Fixing KaryawanSeeder.php..."
# This is complex, will need manual editing or more sophisticated script
echo "⚠️  KaryawanSeeder.php needs manual review - all set to 'aktif', should vary"

# =============================================================================
# FIX 8: tests/Feature/KaryawanTest.php
# =============================================================================
echo "🔨 Fixing KaryawanTest.php..."
sed -i "s/'status_karyawan' => 'aktif'/'status_karyawan' => 'tetap'/g" tests/Feature/KaryawanTest.php
sed -i "s/'status_karyawan' => 'nonaktif'/'status_karyawan' => 'magang'/g" tests/Feature/KaryawanTest.php

# =============================================================================
# FIX 9: tests/Unit/PenggajianServiceTest.php
# =============================================================================
echo "🔨 Fixing PenggajianServiceTest.php..."
sed -i "s/'status_karyawan' => 'aktif'/'status_karyawan' => 'tetap'/g" tests/Unit/PenggajianServiceTest.php

echo ""
echo "✅ All automatic fixes completed!"
echo ""
echo "📋 Summary:"
echo "  - Form dropdowns: ✅ Updated (tetap/kontrak/magang)"
echo "  - Badge displays: ✅ Updated (using Model helpers)"
echo "  - Filters: ✅ Updated"
echo "  - Tests: ✅ Updated"
echo ""
echo "⚠️  Manual review needed:"
echo "  - database/seeders/KaryawanSeeder.php (vary status values)"
echo "  - Run: php artisan migrate:fresh --seed"
echo "  - Run: php artisan test"
echo ""
echo "💾 Backup location: $BACKUP_DIR"
echo ""
echo "🎉 Status Karyawan migration complete!"

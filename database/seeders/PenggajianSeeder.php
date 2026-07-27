<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use App\Models\Penggajian;
use App\Models\Potongan;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PenggajianSeeder extends Seeder
{
    public function run(): void
    {
        $karyawans = Karyawan::with('jabatan')->get();
        $potongans = Potongan::where('status_aktif', true)->get();

        // Distribusi status - hanya 3 status resmi:
        // Draft: 10, Final: 15, Dibayar: 25
        $statusDistribution = array_merge(
            array_fill(0, 10, 'draft'),
            array_fill(0, 15, 'final'),
            array_fill(0, 25, 'dibayar')
        );

        shuffle($statusDistribution);

        // Generate 50 data penggajian dari 12 bulan terakhir
        $startDate = Carbon::now()->subMonths(11)->startOfMonth();
        $generatedCount = 0;
        $usedCombinations = []; // Track karyawan-periode yang sudah digunakan

        for ($monthOffset = 0; $monthOffset < 12; $monthOffset++) {
            $periode = $startDate->copy()->addMonths($monthOffset);
            $karyawansPerMonth = rand(3, 6); // 3-6 karyawan per bulan

            for ($i = 0; $i < $karyawansPerMonth && $generatedCount < 50; $i++) {
                // Cari karyawan yang belum punya penggajian di periode ini
                $attempts = 0;
                do {
                    $karyawan = $karyawans->random();
                    $key = $karyawan->id . '-' . $periode->format('Y-m');
                    $attempts++;
                } while (isset($usedCombinations[$key]) && $attempts < 50);

                // Skip jika sudah mencoba 50 kali (semua karyawan sudah dipakai di periode ini)
                if ($attempts >= 50) continue;

                $usedCombinations[$key] = true;
                $status = $statusDistribution[$generatedCount];

                // Hitung gaji
                $gajiPokok = $karyawan->jabatan->gaji_pokok;
                $tunjangan = $karyawan->jabatan->tunjangan_jabatan;

                // Random potongan (0-4 potongan)
                $jumlahPotongan = rand(0, 4);
                $selectedPotongans = $potongans->random(min($jumlahPotongan, $potongans->count()));

                // Hitung total potongan
                $totalPotongan = 0;
                foreach ($selectedPotongans as $potongan) {
                    if ($potongan->jenis_potongan === 'persentase') {
                        $totalPotongan += ($gajiPokok * $potongan->nilai / 100);
                    } else {
                        $totalPotongan += $potongan->nilai;
                    }
                }

                $gajiBersih = $gajiPokok + $tunjangan - $totalPotongan;

                // Tentukan tanggal bayar hanya untuk status dibayar
                $tanggalBayar = null;
                if ($status === 'dibayar') {
                    $tanggalBayar = $periode->copy()->addDays(rand(25, 30));
                }

                // Catatan berdasarkan status
                $catatan = $this->generateCatatan($status);

                // Create penggajian
                $penggajian = Penggajian::create([
                    'karyawan_id' => $karyawan->id,
                    'periode' => $periode->format('Y-m'),
                    'gaji_pokok' => $gajiPokok,
                    'tunjangan' => $tunjangan,
                    'total_potongan' => $totalPotongan,
                    'gaji_bersih' => $gajiBersih,
                    'status' => $status,
                    'tanggal_bayar' => $tanggalBayar,
                    'catatan' => $catatan,
                ]);

                // Create detail potongans
                foreach ($selectedPotongans as $potongan) {
                    if ($potongan->jenis_potongan === 'persentase') {
                        $nilaiPotongan = $gajiPokok * $potongan->nilai / 100;
                    } else {
                        $nilaiPotongan = $potongan->nilai;
                    }

                    $penggajian->details()->create([
                        'potongan_id' => $potongan->id,
                        'nama_potongan' => $potongan->nama_potongan,
                        'nilai_potongan' => $nilaiPotongan,
                    ]);
                }

                $generatedCount++;
            }
        }
    }

    private function generateCatatan($status): string
    {
        $catatan = [
            'draft' => [
                'Data penggajian masih dalam proses verifikasi.',
                'Menunggu data absensi final.',
                'Draft penggajian bulan ini.',
                'Data masih dapat diedit dan diverifikasi.',
            ],
            'final' => [
                'Data telah dikunci dan siap diproses.',
                'Menunggu proses pembayaran.',
                'Telah diverifikasi dan disetujui.',
                'Data penggajian final, tidak dapat diubah.',
            ],
            'dibayar' => [
                'Gaji telah ditransfer ke rekening karyawan.',
                'Pembayaran berhasil dilakukan.',
                'Transfer gaji sudah diproses.',
                'Pembayaran telah selesai.',
            ],
        ];

        $options = $catatan[$status] ?? ['Tidak ada catatan'];
        return $options[array_rand($options)];
    }
}

<?php

namespace Database\Seeders;

use App\Models\Potongan;
use Illuminate\Database\Seeder;

class PotonganSeeder extends Seeder
{
    public function run(): void
    {
        $potongans = [
            ['nama_potongan' => 'BPJS Kesehatan', 'jenis_potongan' => 'persentase', 'nilai' => 1, 'status_aktif' => true],
            ['nama_potongan' => 'BPJS Ketenagakerjaan', 'jenis_potongan' => 'persentase', 'nilai' => 2, 'status_aktif' => true],
            ['nama_potongan' => 'PPh 21', 'jenis_potongan' => 'persentase', 'nilai' => 5, 'status_aktif' => true],
            ['nama_potongan' => 'Potongan Keterlambatan', 'jenis_potongan' => 'nominal', 'nilai' => 50000, 'status_aktif' => true],
            ['nama_potongan' => 'Iuran Koperasi', 'jenis_potongan' => 'nominal', 'nilai' => 25000, 'status_aktif' => false],
        ];

        foreach ($potongans as $potongan) {
            Potongan::firstOrCreate(['nama_potongan' => $potongan['nama_potongan']], $potongan);
        }
    }
}
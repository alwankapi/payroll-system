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
            ['nama_potongan' => 'Kasbon', 'jenis_potongan' => 'nominal', 'nilai' => 500000, 'status_aktif' => true],
            ['nama_potongan' => 'Keterlambatan', 'jenis_potongan' => 'nominal', 'nilai' => 50000, 'status_aktif' => true],
            ['nama_potongan' => 'Alpha', 'jenis_potongan' => 'nominal', 'nilai' => 100000, 'status_aktif' => true],
            ['nama_potongan' => 'Izin Tidak Dibayar', 'jenis_potongan' => 'nominal', 'nilai' => 150000, 'status_aktif' => true],
            ['nama_potongan' => 'Pinjaman Karyawan', 'jenis_potongan' => 'nominal', 'nilai' => 1000000, 'status_aktif' => true],
            ['nama_potongan' => 'Denda Administrasi', 'jenis_potongan' => 'nominal', 'nilai' => 75000, 'status_aktif' => true],
            ['nama_potongan' => 'Iuran Koperasi', 'jenis_potongan' => 'nominal', 'nilai' => 25000, 'status_aktif' => false],
        ];

        foreach ($potongans as $potongan) {
            Potongan::firstOrCreate(['nama_potongan' => $potongan['nama_potongan']], $potongan);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Database\Seeder;

class KaryawanSeeder extends Seeder
{
    public function run(): void
    {
        $jabatanIds = Jabatan::pluck('id')->all();

        $data = [
            ['email' => 'karyawan1@payroll.test',  'nik' => '3201010101900001', 'no_telepon' => '081234560001', 'tanggal_masuk' => '2022-01-10', 'no_rekening' => '1010000001', 'status_karyawan' => 'aktif'],
            ['email' => 'karyawan2@payroll.test',  'nik' => '3201010101900002', 'no_telepon' => '081234560002', 'tanggal_masuk' => '2022-02-15', 'no_rekening' => '1010000002', 'status_karyawan' => 'aktif'],
            ['email' => 'karyawan3@payroll.test',  'nik' => '3201010101900003', 'no_telepon' => '081234560003', 'tanggal_masuk' => '2022-03-01', 'no_rekening' => '1010000003', 'status_karyawan' => 'aktif'],
            ['email' => 'karyawan4@payroll.test',  'nik' => '3201010101900004', 'no_telepon' => '081234560004', 'tanggal_masuk' => '2022-04-20', 'no_rekening' => '1010000004', 'status_karyawan' => 'aktif'],
            ['email' => 'karyawan5@payroll.test',  'nik' => '3201010101900005', 'no_telepon' => '081234560005', 'tanggal_masuk' => '2022-05-05', 'no_rekening' => '1010000005', 'status_karyawan' => 'aktif'],
            ['email' => 'karyawan6@payroll.test',  'nik' => '3201010101900006', 'no_telepon' => '081234560006', 'tanggal_masuk' => '2023-01-12', 'no_rekening' => '1010000006', 'status_karyawan' => 'aktif'],
            ['email' => 'karyawan7@payroll.test',  'nik' => '3201010101900007', 'no_telepon' => '081234560007', 'tanggal_masuk' => '2023-02-18', 'no_rekening' => '1010000007', 'status_karyawan' => 'aktif'],
            ['email' => 'karyawan8@payroll.test',  'nik' => '3201010101900008', 'no_telepon' => '081234560008', 'tanggal_masuk' => '2023-03-25', 'no_rekening' => '1010000008', 'status_karyawan' => 'aktif'],
            ['email' => 'karyawan9@payroll.test',  'nik' => '3201010101900009', 'no_telepon' => '081234560009', 'tanggal_masuk' => '2023-04-30', 'no_rekening' => '1010000009', 'status_karyawan' => 'aktif'],
            ['email' => 'karyawan10@payroll.test', 'nik' => '3201010101900010', 'no_telepon' => '081234560010', 'tanggal_masuk' => '2021-11-11', 'no_rekening' => '1010000010', 'status_karyawan' => 'nonaktif'],
        ];

        foreach ($data as $index => $item) {
            $user = User::where('email', $item['email'])->firstOrFail();

            Karyawan::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'jabatan_id' => $jabatanIds[$index % count($jabatanIds)],
                    'nik' => $item['nik'],
                    'nama_lengkap' => $user->name,
                    'alamat' => 'Jl. Contoh No. ' . ($index + 1) . ', Jakarta',
                    'no_telepon' => $item['no_telepon'],
                    'tanggal_masuk' => $item['tanggal_masuk'],
                    'no_rekening' => $item['no_rekening'],
                    'status_karyawan' => $item['status_karyawan'],
                ]
            );
        }
    }
}
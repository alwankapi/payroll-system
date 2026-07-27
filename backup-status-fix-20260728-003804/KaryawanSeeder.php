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
            ['email' => 'karyawan1@payroll.test',  'nik' => '3201010101900001', 'no_telepon' => '081234560001', 'tanggal_masuk' => '2021-01-10', 'no_rekening' => '1010000001', 'status_karyawan' => 'aktif', 'alamat' => 'Jl. Merdeka No. 1, Jakarta Pusat'],
            ['email' => 'karyawan2@payroll.test',  'nik' => '3201010101900002', 'no_telepon' => '081234560002', 'tanggal_masuk' => '2021-02-15', 'no_rekening' => '1010000002', 'status_karyawan' => 'aktif', 'alamat' => 'Jl. Sudirman No. 2, Jakarta Selatan'],
            ['email' => 'karyawan3@payroll.test',  'nik' => '3201010101900003', 'no_telepon' => '081234560003', 'tanggal_masuk' => '2021-03-20', 'no_rekening' => '1010000003', 'status_karyawan' => 'aktif', 'alamat' => 'Jl. Thamrin No. 3, Jakarta Pusat'],
            ['email' => 'karyawan4@payroll.test',  'nik' => '3201010101900004', 'no_telepon' => '081234560004', 'tanggal_masuk' => '2021-04-05', 'no_rekening' => '1010000004', 'status_karyawan' => 'aktif', 'alamat' => 'Jl. Gatot Subroto No. 4, Jakarta Selatan'],
            ['email' => 'karyawan5@payroll.test',  'nik' => '3201010101900005', 'no_telepon' => '081234560005', 'tanggal_masuk' => '2021-05-12', 'no_rekening' => '1010000005', 'status_karyawan' => 'aktif', 'alamat' => 'Jl. Kuningan No. 5, Jakarta Selatan'],
            ['email' => 'karyawan6@payroll.test',  'nik' => '3201010101900006', 'no_telepon' => '081234560006', 'tanggal_masuk' => '2021-06-18', 'no_rekening' => '1010000006', 'status_karyawan' => 'aktif', 'alamat' => 'Jl. Rasuna Said No. 6, Jakarta Selatan'],
            ['email' => 'karyawan7@payroll.test',  'nik' => '3201010101900007', 'no_telepon' => '081234560007', 'tanggal_masuk' => '2022-01-10', 'no_rekening' => '1010000007', 'status_karyawan' => 'aktif', 'alamat' => 'Jl. MT Haryono No. 7, Jakarta Selatan'],
            ['email' => 'karyawan8@payroll.test',  'nik' => '3201010101900008', 'no_telepon' => '081234560008', 'tanggal_masuk' => '2022-02-14', 'no_rekening' => '1010000008', 'status_karyawan' => 'aktif', 'alamat' => 'Jl. Salemba No. 8, Jakarta Pusat'],
            ['email' => 'karyawan9@payroll.test',  'nik' => '3201010101900009', 'no_telepon' => '081234560009', 'tanggal_masuk' => '2022-03-20', 'no_rekening' => '1010000009', 'status_karyawan' => 'aktif', 'alamat' => 'Jl. Cikini No. 9, Jakarta Pusat'],
            ['email' => 'karyawan10@payroll.test', 'nik' => '3201010101900010', 'no_telepon' => '081234560010', 'tanggal_masuk' => '2022-04-15', 'no_rekening' => '1010000010', 'status_karyawan' => 'aktif', 'alamat' => 'Jl. Menteng No. 10, Jakarta Pusat'],
            ['email' => 'karyawan11@payroll.test', 'nik' => '3201010101900011', 'no_telepon' => '081234560011', 'tanggal_masuk' => '2022-05-22', 'no_rekening' => '1010000011', 'status_karyawan' => 'aktif', 'alamat' => 'Jl. Kemang No. 11, Jakarta Selatan'],
            ['email' => 'karyawan12@payroll.test', 'nik' => '3201010101900012', 'no_telepon' => '081234560012', 'tanggal_masuk' => '2022-06-10', 'no_rekening' => '1010000012', 'status_karyawan' => 'aktif', 'alamat' => 'Jl. Pancoran No. 12, Jakarta Selatan'],
            ['email' => 'karyawan13@payroll.test', 'nik' => '3201010101900013', 'no_telepon' => '081234560013', 'tanggal_masuk' => '2023-01-15', 'no_rekening' => '1010000013', 'status_karyawan' => 'aktif', 'alamat' => 'Jl. Tebet No. 13, Jakarta Selatan'],
            ['email' => 'karyawan14@payroll.test', 'nik' => '3201010101900014', 'no_telepon' => '081234560014', 'tanggal_masuk' => '2023-02-20', 'no_rekening' => '1010000014', 'status_karyawan' => 'aktif', 'alamat' => 'Jl. Kebayoran No. 14, Jakarta Selatan'],
            ['email' => 'karyawan15@payroll.test', 'nik' => '3201010101900015', 'no_telepon' => '081234560015', 'tanggal_masuk' => '2023-03-10', 'no_rekening' => '1010000015', 'status_karyawan' => 'aktif', 'alamat' => 'Jl. Pondok Indah No. 15, Jakarta Selatan'],
            ['email' => 'karyawan16@payroll.test', 'nik' => '3201010101900016', 'no_telepon' => '081234560016', 'tanggal_masuk' => '2023-04-05', 'no_rekening' => '1010000016', 'status_karyawan' => 'aktif', 'alamat' => 'Jl. Cilandak No. 16, Jakarta Selatan'],
            ['email' => 'karyawan17@payroll.test', 'nik' => '3201010101900017', 'no_telepon' => '081234560017', 'tanggal_masuk' => '2023-05-12', 'no_rekening' => '1010000017', 'status_karyawan' => 'aktif', 'alamat' => 'Jl. Fatmawati No. 17, Jakarta Selatan'],
            ['email' => 'karyawan18@payroll.test', 'nik' => '3201010101900018', 'no_telepon' => '081234560018', 'tanggal_masuk' => '2023-06-18', 'no_rekening' => '1010000018', 'status_karyawan' => 'aktif', 'alamat' => 'Jl. Blok M No. 18, Jakarta Selatan'],
            ['email' => 'karyawan19@payroll.test', 'nik' => '3201010101900019', 'no_telepon' => '081234560019', 'tanggal_masuk' => '2024-01-10', 'no_rekening' => '1010000019', 'status_karyawan' => 'aktif', 'alamat' => 'Jl. Senayan No. 19, Jakarta Pusat'],
            ['email' => 'karyawan20@payroll.test', 'nik' => '3201010101900020', 'no_telepon' => '081234560020', 'tanggal_masuk' => '2024-02-15', 'no_rekening' => '1010000020', 'status_karyawan' => 'aktif', 'alamat' => 'Jl. Permata Hijau No. 20, Jakarta Selatan'],
            ['email' => 'karyawan21@payroll.test', 'nik' => '3201010101900021', 'no_telepon' => '081234560021', 'tanggal_masuk' => '2024-03-10', 'no_rekening' => '1010000021', 'status_karyawan' => 'aktif', 'alamat' => 'Jl. Pejaten No. 21, Jakarta Selatan'],
            ['email' => 'karyawan22@payroll.test', 'nik' => '3201010101900022', 'no_telepon' => '081234560022', 'tanggal_masuk' => '2024-04-20', 'no_rekening' => '1010000022', 'status_karyawan' => 'aktif', 'alamat' => 'Jl. Depok No. 22, Depok'],
            ['email' => 'karyawan23@payroll.test', 'nik' => '3201010101900023', 'no_telepon' => '081234560023', 'tanggal_masuk' => '2024-05-15', 'no_rekening' => '1010000023', 'status_karyawan' => 'aktif', 'alamat' => 'Jl. Bekasi No. 23, Bekasi'],
            ['email' => 'karyawan24@payroll.test', 'nik' => '3201010101900024', 'no_telepon' => '081234560024', 'tanggal_masuk' => '2025-01-10', 'no_rekening' => '1010000024', 'status_karyawan' => 'aktif', 'alamat' => 'Jl. Tangerang No. 24, Tangerang'],
            ['email' => 'karyawan25@payroll.test', 'nik' => '3201010101900025', 'no_telepon' => '081234560025', 'tanggal_masuk' => '2025-02-18', 'no_rekening' => '1010000025', 'status_karyawan' => 'aktif', 'alamat' => 'Jl. Bogor No. 25, Bogor'],
        ];

        foreach ($data as $index => $item) {
            $user = User::where('email', $item['email'])->firstOrFail();

            Karyawan::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'jabatan_id' => $jabatanIds[$index % count($jabatanIds)],
                    'nik' => $item['nik'],
                    'nama_lengkap' => $user->name,
                    'alamat' => $item['alamat'],
                    'no_telepon' => $item['no_telepon'],
                    'tanggal_masuk' => $item['tanggal_masuk'],
                    'no_rekening' => $item['no_rekening'],
                    'status_karyawan' => $item['status_karyawan'],
                ]
            );
        }
    }
}

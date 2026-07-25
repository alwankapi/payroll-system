<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@payroll.test'],
            [
                'name' => 'Admin HRD',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        $namaKaryawan = [
            'Andi Saputra',
            'Budi Santoso',
            'Citra Lestari',
            'Deni Pratama',
            'Eka Putri',
            'Fajar Hidayat',
            'Gita Permata',
            'Hendra Wijaya',
            'Indah Sari',
            'Joko Susilo',
            'Kartika Dewi',
            'Lukman Hakim',
            'Maya Anggraini',
            'Nanda Firmansyah',
            'Oktavia Ningsih',
            'Pandu Setiawan',
            'Qory Sandrina',
            'Rina Marlina',
            'Siti Nurhaliza',
            'Tono Suryanto',
            'Umar Bakri',
            'Vina Melinda',
            'Wulan Sari',
            'Yusuf Maulana',
            'Zahra Kamila',
        ];

        foreach ($namaKaryawan as $index => $nama) {
            $nomor = $index + 1;
            User::firstOrCreate(
                ['email' => "karyawan{$nomor}@payroll.test"],
                [
                    'name' => $nama,
                    'password' => Hash::make('password'),
                    'role' => 'karyawan',
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}

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
            'Budi Santoso', 'Siti Nurhaliza', 'Agus Wijaya', 'Dewi Lestari', 'Rudi Hartono',
            'Rina Marlina', 'Eko Prasetyo', 'Fitri Handayani', 'Joko Susilo', 'Wulan Sari',
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
<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin Dinas',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Pegawai 1',
            'email' => 'pegawai@example.com',
            'password' => Hash::make('pegawai123'),
            'role' => 'pegawai',
        ]);

        User::create([
            'name' => 'Pimpinan',
            'email' => 'pimpinan@example.com',
            'password' => Hash::make('pimpinan123'),
            'role' => 'pimpinan',
        ]);
    }
}

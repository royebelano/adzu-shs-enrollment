<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admissions Office Account
        User::create([
            'name' => 'Admissions Staff',
            'email' => 'admissions@adzu.edu.ph',
            'password' => Hash::make('password123'),
            'role' => 'Staff',
            'department' => 'Admissions',
        ]);

        // 2. Infirmary / Clinic Account
        User::create([
            'name' => 'School Nurse / Infirmary Staff',
            'email' => 'infirmary@adzu.edu.ph',
            'password' => Hash::make('password123'),
            'role' => 'Staff',
            'department' => 'Infirmary',
        ]);

        // 3. Finance / Accounting Account
        User::create([
            'name' => 'Finance Officer',
            'email' => 'finance@adzu.edu.ph',
            'password' => Hash::make('password123'),
            'role' => 'Staff',
            'department' => 'Finance',
        ]);

        // 4. Guidance Office Account
        User::create([
            'name' => 'Guidance Counselor',
            'email' => 'guidance@adzu.edu.ph',
            'password' => Hash::make('password123'),
            'role' => 'Staff',
            'department' => 'Guidance',
        ]);

        // 5. Registrar Account
        User::create([
            'name' => 'Registrar Staff',
            'email' => 'registrar@adzu.edu.ph',
            'password' => Hash::make('password123'),
            'role' => 'Staff',
            'department' => 'Registrar',
        ]);
    }
}

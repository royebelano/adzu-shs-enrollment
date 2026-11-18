<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateUserAccount extends Command
{
    // Ang command na ita-type sa terminal
    protected $signature = 'user:create-custom';

    // Deskripsyon ng command
    protected $description = 'Interaktibong paggawa ng user account para sa iba-ibang departamento (Admissions, Infirmary, etc.)';

    public function handle()
    {
        $this->info('--- Pagbuo ng Bagong Account para sa AdZU SHS e-Admissions ---');

        // Tanungin ang mga detalye sa terminal
        $name = $this->ask('Ilagay ang Pangalan ng User/Staff:');
        $email = $this->ask('Ilagay ang Email Address:');
        $password = $this->secret('Ilagay ang Password:');

        $role = $this->choice('Pumili ng Role:', ['Admin', 'Staff', 'Applicant'], 1);

        $department = $this->choice('Pumili ng Departamento:', [
            'Admissions',
            'Infirmary',
            'Guidance',
            'Finance',
            'Registrar',
            'None'
        ], 0);

        // I-save sa database
        User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => $role,
            'department' => $department,
        ]);

        $this->info("Tagumpay! Na-create na ang account para kay {$name} ({$department} Department).");
    }
}

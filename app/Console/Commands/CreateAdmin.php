<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdmin extends Command
{
    protected $signature = 'admin:create {email? : Email ng admin} {--name=Admin : Pangalan}';

    protected $description = 'Gumawa ng admin account (o palitan ang password nito)';

    public function handle(): int
    {
        $email    = $this->argument('email') ?: $this->ask('Email ng admin');
        $password = $this->secret('Password (hindi bababa sa 8 characters)');
        $confirm  = $this->secret('Ulitin ang password');

        if ($password !== $confirm) {
            $this->error('Hindi magkapareho ang dalawang password.');

            return self::FAILURE;
        }

        $validator = Validator::make(
            ['email' => $email, 'password' => $password],
            ['email' => ['required', 'email'], 'password' => ['required', 'min:8']]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        User::updateOrCreate(
            ['email' => $email],
            ['name' => $this->option('name'), 'password' => Hash::make($password)]
        );

        $this->info("Handa na ang admin account: {$email}");

        return self::SUCCESS;
    }
}

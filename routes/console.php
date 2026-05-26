<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('initiative:create-admin {email} {--name=Admin}', function (string $email) {
    validator(['email' => $email], ['email' => ['required', 'email', 'unique:users,email']])->validate();

    $password = $this->secret('Admin password');
    $confirmation = $this->secret('Confirm password');

    if ($password !== $confirmation) {
        throw ValidationException::withMessages(['password' => 'Password confirmation does not match.']);
    }

    User::create([
        'name' => $this->option('name'),
        'email' => $email,
        'password' => Hash::make($password),
        'role' => 'admin',
    ]);

    $this->info('Admin user created. The pledge will be required on first dashboard access.');
})->purpose('Create the first initiative admin user');

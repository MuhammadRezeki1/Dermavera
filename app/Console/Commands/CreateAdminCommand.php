<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CreateAdminCommand extends Command
{
    protected $signature = 'dermavera:create-admin {email?}';

    protected $description = 'Membuat atau mempromosikan admin tanpa menyimpan kredensial di seeder.';

    public function handle(): int
    {
        $email = $this->argument('email') ?: $this->ask('Email admin');
        $name = $this->ask('Nama admin');
        $password = $this->secret('Password (minimal 12 karakter)');
        Validator::make(compact('email', 'name', 'password'), ['email' => ['required', 'email'], 'name' => ['required', 'string', 'max:255'], 'password' => ['required', Password::min(12)->mixedCase()->numbers()->symbols()]])->validate();
        $user = User::firstOrNew(['email' => $email]);
        $user->forceFill(['name' => $name, 'password' => Hash::make($password), 'email_verified_at' => now(), 'is_admin' => true])->save();
        $this->info('Admin berhasil dibuat.');

        return self::SUCCESS;
    }
}

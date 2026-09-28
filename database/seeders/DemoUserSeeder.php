<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        $password = env('DEMO_ADMIN_PASSWORD');
        if (! is_string($password) || strlen($password) < 12) {
            $this->command?->warn('Demo user dilewati: DEMO_ADMIN_PASSWORD belum diatur atau kurang dari 12 karakter.');

            return;
        }

        $user = User::query()->firstOrNew([
            'email' => 'admin@dermavera.test',
        ]);

        $user->name = 'Dermavera Admin';
        $user->password = Hash::make($password);
        $user->email_verified_at = now();
        $user->is_admin = true;
        $user->save();
    }
}

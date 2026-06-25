<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Cria/atualiza o ÚNICO administrador do site. Senha inicial vem de
 * ADMIN_PASSWORD (.env) ou é gerada aleatoriamente e impressa no terminal.
 * O admin é obrigado a trocar a senha no primeiro acesso (must_change_password).
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'samirhv@me.com');
        $password = env('ADMIN_PASSWORD') ?: Str::password(16);

        $user = User::firstWhere('email', $email) ?? new User;

        $isNew = ! $user->exists;

        $user->fill([
            'name' => env('ADMIN_NAME', 'Samir Hanna Verza'),
            'email' => $email,
        ]);
        $user->is_admin = true;

        // Só (re)define a senha em criação ou quando ADMIN_PASSWORD é informado.
        if ($isNew || env('ADMIN_PASSWORD')) {
            $user->password = Hash::make($password);
            $user->must_change_password = true;
        }

        $user->save();

        $this->command?->info('Admin '.($isNew ? 'criado' : 'atualizado').': '.$email);
        if ($isNew || env('ADMIN_PASSWORD')) {
            $this->command?->warn('Senha inicial: '.$password.'  (troca obrigatória no 1º acesso)');
        }
    }
}

<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'login' => 'root',
            'password' => Hash::make('superroot'),
            'role' => 'root',
            'enabled' => true,
            'validated' => true,
            'note' => 'It is automatically created root'
        ]);
        User::create([
            'login' => 'admin',
            'password' => Hash::make('superadmin'),
            'role' => 'admin',
            'enabled' => true,
            'validated' => true,
            'note' => 'It is automatically created admin'
        ]);
        User::create([
            'login' => 'user',
            'password' => Hash::make('superuser'),
            'role' => 'user',
            'enabled' => true,
            'validated' => true,
            'note' => 'It is automatically created user'
        ]);

    }
}

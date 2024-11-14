<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::create([
            'name' => 'Admin',
            'email' => 'admin@app.com',
            'password' => bcrypt('password'),
        ]);

        $adminUser->assignRole('admin');

        UserProfile::create([
            'user_id' => $adminUser->id,
        ]);

        // Create a regular user
        $user = User::create([
            'name' => 'User',
            'email' => 'user@app.com',
            'password' => bcrypt('password'),
        ]);

        UserProfile::create([
            'user_id' => $user->id,
        ]);
    }
}

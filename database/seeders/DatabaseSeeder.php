<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. De Admin User aanmaken
        User::create([
            'name' => 'Hoofdbeheerder',
            'email' => 'admin@animewebshop.be',
            'password' => Hash::make('password123'),
            'role' => UserRole::Admin,
        ]);

    }
}

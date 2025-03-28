<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Notebook;
use Illuminate\Database\Seeder;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test user with predefined credentials
        $user = User::factory()
            ->testUser()
            ->create();

        // Create 15 notebooks for the test user with varied categories
        Notebook::factory()
            ->count(15)
            ->for($user)
            ->create();

        // Output information about the created test user
        $this->command->info('Test User Created:');
        $this->command->info('Email: test@example.com');
        $this->command->info('Password: password');
        $this->command->info('Number of notebooks created: 15');
    }
}

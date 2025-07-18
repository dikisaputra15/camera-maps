<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            // Check if the admin user already exists to avoid duplicates
            $admin = User::firstOrCreate(
                ['email' => 'admin@gmail.com'],
                [
                    'name' => 'Administrator',
                    'password' => Hash::make('password'),
                ]
            );

            // Ensure the 'admin' role exists
            $adminRole = Role::firstOrCreate(['name' => 'admin']);

            // Assign the 'admin' role to the user
            if (!$admin->hasRole('admin')) {
                $admin->assignRole($adminRole);
            }

            $this->command->info('Admin user seeded successfully.');
        } catch (Exception $e) {
            $this->command->error('Failed to seed admin user: ' . $e->getMessage());
        }
    }
}

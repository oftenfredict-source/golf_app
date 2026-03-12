<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'System Administrator',
                'email' => 'admin@golfclub.com',
                'password' => 'admin123',
            ],
            [
                'name' => 'John Doe',
                'email' => 'manager@golfclub.com',
                'password' => 'manager123',
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'operations@golfclub.com',
                'password' => 'operations123',
            ],
            [
                'name' => 'Michael Johnson',
                'email' => 'finance@golfclub.com',
                'password' => 'finance123',
            ],
            [
                'name' => 'Sarah Williams',
                'email' => 'counter.premium@golfclub.com',
                'password' => 'counter123',
            ],
            [
                'name' => 'David Brown',
                'email' => 'counter.regular@golfclub.com',
                'password' => 'counter123',
            ],
            [
                'name' => 'Robert Taylor',
                'email' => 'golf.ops@golfclub.com',
                'password' => 'golf123',
            ],
            [
                'name' => 'Emily Davis',
                'email' => 'equipment@golfclub.com',
                'password' => 'equipment123',
            ],
            [
                'name' => 'James Wilson',
                'email' => 'access.control@golfclub.com',
                'password' => 'access123',
            ],
            [
                'name' => 'Linda Anderson',
                'email' => 'waiter@golfclub.com',
                'password' => 'waiter123',
            ],
            [
                'name' => 'Test User',
                'email' => 'test@golfclub.com',
                'password' => 'test123',
            ],
        ];

        $created = 0;
        $updated = 0;

        foreach ($users as $userData) {
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make($userData['password']),
                    'email_verified_at' => now(),
                ]
            );

            if ($user->wasRecentlyCreated) {
                $created++;
            } else {
                $updated++;
            }
        }

        $this->command->info("Users seeded successfully!");
        $this->command->info("Created: {$created} new users");
        $this->command->info("Updated: {$updated} existing users");
        $this->command->info("\nDefault password format: [role]123 (e.g., admin123, manager123)");
        $this->command->warn("\n⚠️  Remember to change default passwords before production!");
    }
}


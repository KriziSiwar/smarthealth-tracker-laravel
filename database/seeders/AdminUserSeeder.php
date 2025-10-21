<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Update the user with email 'siwarkrizi09@gmail.com' to have admin role
        $user = User::where('email', 'siwarkrizi09@gmail.com')->first();
        
        if ($user) {
            $user->update([
                'role' => 'admin'
            ]);
            $this->command->info('Admin role has been assigned to siwarkrizi09@gmail.com');
        } else {
            // Create the admin user if it doesn't exist
            User::create([
                'name' => 'Admin User',
                'email' => 'siwarkrizi09@gmail.com',
                'password' => Hash::make('Siwar123./'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]);
            $this->command->info('Admin user created with email siwarkrizi09@gmail.com');
        }
    }
}

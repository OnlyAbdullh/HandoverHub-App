<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!User::where('name', 'Super Admin')->exists()) {
            $admin = User::create([
                'name' => 'Super Admin',
                'password' => Hash::make('12345678'),
            ]);

            $admin->assignRole('user_manager');
            echo " created successfully\n";
        } else {
            echo "already exist\n";
        }
    }
}

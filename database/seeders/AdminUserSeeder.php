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
        if (!User::where('username', 'Super Admin')->exists()) {
            $admin = User::create([
                'username' => 'Super Admin',
                'password' => Hash::make('12345678'),
            ]);

            $admin->assignRole('manager');
            echo " created successfully\n";
        } else {
            echo "already exist\n";
        }
    }
}

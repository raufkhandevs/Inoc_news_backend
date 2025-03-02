<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createAdmin();
    }

    private function createAdmin()
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@newsaggregator.com',
            'password' => Hash::make('password'), // TODO: Change this to a more secure password
            'is_admin' => true,
        ]);
    }
}

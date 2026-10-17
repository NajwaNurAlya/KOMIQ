<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Administrator KOMIQ',
                'email' => 'admin@komiq.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'coin_balance' => 9999,
                'is_active' => true,
            ],
            [
                'name' => 'Kreator Webtoon',
                'email' => 'kreator@komiq.com',
                'password' => Hash::make('password123'),
                'role' => 'kreator',
                'coin_balance' => 250,
                'is_active' => true,
            ],
            [
                'name' => 'Pembaca Komik',
                'email' => 'user@komiq.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'coin_balance' => 100,
                'is_active' => true,
            ],
            [
                'name' => 'Irma Nurhayati',
                'email' => 'irman4970@gmail.com',
                'password' => Hash::make('password123'),
                'role' => 'admin', // Irma sebagai PIC modul User/Auth diberi role admin untuk kemudahan presentasi
                'coin_balance' => 500,
                'is_active' => true,
            ],
            [
                'name' => 'Akun Terblokir',
                'email' => 'blocked@komiq.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'coin_balance' => 0,
                'is_active' => false,
            ],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                $data
            );
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\Division;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin System',
                'email' => 'sebastianusdiaz@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'department' => 'IT',
            ],
            [
                'name' => 'Pegawai Kantor',
                'email' => 'Veronikasilviyanti@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'pegawai',
                'department' => 'HRD',
            ],
            [
                'name' => 'Wartawan Media',
                'email' => 'darisawalistyo@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'wartawan',
                'department' => 'Redaksi',
            ],
            [
                'name' => 'Direktur Utama',
                'email' => 'direktur@gmail.com',
                'password' => Hash::make('password'),
                'roles' => ['direktur'],
                'department' => 'Management',
            ],
            [
                'name' => 'Wartawan Merangkap Pegawai',
                'email' => 'multirole@gmail.com',
                'password' => Hash::make('password'),
                'roles' => ['pegawai', 'wartawan'],
                'department' => 'Redaksi',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => $userData['password'],
                ]
            );

            $user->syncRoles($userData['roles'] ?? [$userData['role']]);

            UserProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nip' => rand(10000, 99999),
                    'phone' => '08123456789',
                    'division_id' => Division::where('name', $userData['department'])->first()?->id,
                    'address' => 'Jl. Contoh No. 123',
                    'is_active' => true,
                ]
            );
        }
    }
}

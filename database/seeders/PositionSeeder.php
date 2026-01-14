<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Position;
use Illuminate\Support\Str;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        $positions = [
            'Staff',
            'Senior Staff',
            'Supervisor',
            'Manager',
            'General Manager',
            'Direktur',
            'Editor',
            'Wartawan',
            'Office Boy',
            'Satpam'
        ];

        foreach ($positions as $pos) {
            Position::firstOrCreate(
                ['slug' => Str::slug($pos)],
                ['name' => $pos]
            );
        }
    }
}

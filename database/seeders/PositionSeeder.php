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
            'Direktur',
            'EditorLayout',
            'EditorBerita',
            'EditorFoto',
            'Wartawan',
            'IT Support',
        ];

        foreach ($positions as $pos) {
            Position::firstOrCreate(
                ['slug' => Str::slug($pos)],
                ['name' => $pos]
            );
        }
    }
}

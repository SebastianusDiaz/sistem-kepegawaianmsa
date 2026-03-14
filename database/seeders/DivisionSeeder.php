<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Division;
use Illuminate\Support\Str;

class DivisionSeeder extends Seeder
{
    public function run(): void
    {
        $divisions = ['Redaksi', 'IT', 'Editorial','Direktur'];

        foreach ($divisions as $div) {
            Division::firstOrCreate(
                ['slug' => Str::slug($div)],
                ['name' => $div]
            );
        }
    }
}

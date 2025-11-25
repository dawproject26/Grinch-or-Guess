<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Roulette;

class RouletteSeeder extends Seeder
{
    public function run(): void
    {
        $options = [
            'VOCAL',
            'VOCAL',
            'CONSONANTE',
            'CONSONANTE',
            'VECNA',
            'DEMOGORGON',
            'DEMOPERRO',
            'ELEVEN'
        ];

        foreach ($options as $opt) {
            Roulette::create([
                'option' => $opt
            ]);
        }
    }
}

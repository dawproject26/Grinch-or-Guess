<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
<<<<<<< HEAD
=======
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\PanelSeeder;

>>>>>>> ed8225db0ed5e46e9eadb2935b1cf6ca9e49c762

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
        PanelSeeder::class,  // Línea para llamar al seeder de Panel
    ]);
        User::factory()->create([   
<<<<<<< HEAD
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
=======
           
        ]);
    }
}
>>>>>>> ed8225db0ed5e46e9eadb2935b1cf6ca9e49c762

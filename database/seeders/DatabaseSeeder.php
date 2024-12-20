<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    public function run()
    {
        $this->call(CurrentBeltsTableSeeder::class);
        $this->call(ClassTableSeeder::class);
        
        // Call other seeders here if needed
    }
}

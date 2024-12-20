<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrentBeltsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('current_belts')->insert([
            ['BeltLevel' => '10th Kup', 'BeltName' => 'White Belt', 'Description' => 'Junior Grades'],
            ['BeltLevel' => '9th Kup', 'BeltName' => 'White Belt With Yellow Stripe', 'Description' => 'Junior Grades'],
            ['BeltLevel' => '8th Kup', 'BeltName' => 'Yellow Belt', 'Description' => 'Junior Grades'],
            ['BeltLevel' => '7th Kup', 'BeltName' => 'Yellow Belt With Green Stripe', 'Description' => 'Junior Grades'],
            ['BeltLevel' => '6th Kup', 'BeltName' => 'Green Belt', 'Description' => 'Intermediate'],
            ['BeltLevel' => '5th Kup', 'BeltName' => 'Green Belt With Blue Stripe', 'Description' => 'Intermediate'],
            ['BeltLevel' => '4th Kup', 'BeltName' => 'Blue Belt', 'Description' => 'Intermediate'],
            ['BeltLevel' => '3rd Kup', 'BeltName' => 'Blue Belt With Red Stripe', 'Description' => 'Advanced'],
            ['BeltLevel' => '2nd Kup', 'BeltName' => 'Red Belt', 'Description' => 'Advanced'],
            ['BeltLevel' => '1st Kup', 'BeltName' => 'Red Belt With Black Stripe', 'Description' => 'Advanced'],
            ['BeltLevel' => '1st Dan', 'BeltName' => 'Black Belt', 'Description' => 'Boo-Sabum'],
            ['BeltLevel' => '2nd Dan', 'BeltName' => 'Black Belt', 'Description' => 'Boo-Sabum'],
            ['BeltLevel' => '3rd Dan', 'BeltName' => 'Black Belt', 'Description' => 'Boo-Sabum'],
            ['BeltLevel' => '4th Dan', 'BeltName' => 'Black Belt', 'Description' => 'Sabum'],
            ['BeltLevel' => '5th Dan', 'BeltName' => 'Black Belt', 'Description' => 'Sabum'],
            ['BeltLevel' => '6th Dan', 'BeltName' => 'Black Belt', 'Description' => 'Sabum'],
            ['BeltLevel' => '7th Dan Master', 'BeltName' => 'Black Belt', 'Description' => 'Sahyun'],
            ['BeltLevel' => '8th Dan Master', 'BeltName' => 'Black Belt', 'Description' => 'Sahyun'],
            ['BeltLevel' => '9th Dan Grand Master', 'BeltName' => 'Black Belt', 'Description' => 'Saseong'],
        ]);
    }
}
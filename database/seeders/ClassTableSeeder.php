<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('classes')->insert([
            ['class_day' => 'Monday', 'class_time' => '8:00am to 10:00am'],
            ['class_day' => 'Tuesday', 'class_time' => '8:00am to 10:00am'],
            ['class_day' => 'Wednesday', 'class_time' => '8:00am to 10:00am'],
            ['class_day' => 'Thursday', 'class_time' => '8:00am to 10:00am'],
            ['class_day' => 'Friday', 'class_time' => '8:00am to 10:00am'],
            ['class_day' => 'Saturday', 'class_time' => '8:00am to 10:00am'],
            ['class_day' => 'Sunday', 'class_time' => '8:00am to 10:00am'],
        ]);
    }
}
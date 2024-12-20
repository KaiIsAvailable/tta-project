<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id('student_id'); // Auto-incrementing primary key for the student
            $table->string('name'); // Student's name
            $table->string('ic_number'); // Student's IC number
            $table->string('hp_number'); // Student's HP number
            $table->string('profile_picture')->nullable(); // Path to the student's profile picture (nullable)
            $table->unsignedBigInteger('belt_id'); // Foreign key to the current_belts table
            //$table->date('date_joined'); // Date the student joined
            $table->timestamps(); // Created and updated timestamps
        
            // Foreign key constraint
            $table->foreign('belt_id')->references('BeltID')->on('current_belts')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('students'); // Drop the students table if it exists
    }
};

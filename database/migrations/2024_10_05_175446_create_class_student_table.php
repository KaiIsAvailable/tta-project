<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassStudentTable extends Migration
{
    public function up()
    {
        Schema::create('class_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade'); // Correctly set up foreign key
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade'); // Correctly set up foreign key
        });
    }

    public function down()
    {
        Schema::dropIfExists('class_student');
    }
}
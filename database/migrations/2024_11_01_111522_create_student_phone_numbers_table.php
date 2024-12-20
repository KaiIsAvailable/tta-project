<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentPhoneNumbersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_phone_numbers', function (Blueprint $table) {
            $table->id('phone_id'); // Auto-incrementing ID
            $table->unsignedBigInteger('student_id'); // Foreign key for students
            $table->string('phone_number'); // To store the phone number
            $table->string('phone_person'); // To store the person's name
            $table->timestamps(); // For created_at and updated_at

            // Optional: Add foreign key constraint (assuming you have a students table)
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_phone_numbers');
    }
}
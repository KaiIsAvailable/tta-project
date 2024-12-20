<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('attendance', function (Blueprint $table) {
            $table->id('attendance_id'); // Primary Key
            $table->foreignId('student_id')->constrained('students', 'student_id'); // Foreign Key referencing students
            $table->date('attendance_date'); // Date of attendance
            $table->enum('status', ['present', 'absent', 'excused']); // Attendance status
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};

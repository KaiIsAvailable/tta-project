<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->unsignedBigInteger('centre_id')->nullable(); // Foreign key for the student center
            $table->foreign('centre_id')->references('centre_id')->on('student_centre')->onDelete('cascade'); // Add foreign key constraint
        });
    }

    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['centre_id']); // Drop foreign key constraint
            $table->dropColumn('centre_id'); // Drop the column
        });
    }
};
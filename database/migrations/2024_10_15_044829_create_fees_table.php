<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeesTable extends Migration
{
    public function up()
    {
        Schema::create('fees', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID
            $table->foreignId('student_id')->constrained()->onDelete('cascade'); // Foreign key to students table
            $table->decimal('amount', 8, 2); // Amount of the fee
            $table->date('due_date'); // Due date for the fee
            $table->boolean('paid')->default(false); // Status of the fee payment
        });
    }

    public function down()
    {
        Schema::dropIfExists('fees');
    }
}
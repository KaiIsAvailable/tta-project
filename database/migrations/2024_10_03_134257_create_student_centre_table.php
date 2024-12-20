<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('student_centre', function (Blueprint $table) {
            $table->id('centre_id');
            $table->string('centre_name'); // Name of the center
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_centre');
    }
};
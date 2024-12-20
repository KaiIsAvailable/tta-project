<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('current_belts', function (Blueprint $table) {
            $table->id('BeltID');  // Auto-incrementing primary key
            $table->string('BeltName'); // Name of the belt
            $table->string('BeltLevel'); // Corrected to string for levels like '10th Kup', '1st Dan', etc.
            $table->text('Description'); // Description for the belt
        });
    }

    public function down()
    {
        Schema::dropIfExists('current_belts');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lab_events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('category', ['work', 'seminar', 'other'])->default('work');
            $table->dateTime('start');
            $table->dateTime('end');
            $table->text('description')->nullable();
            $table->timestamps();
           
        });
    }

    public function down()
    {
        Schema::dropIfExists('lab_events');
    }
};

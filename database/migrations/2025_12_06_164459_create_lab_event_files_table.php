<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::create('lab_event_files', function (Blueprint $table) {
        $table->id();
        $table->foreignId('lab_event_id')
            ->constrained('lab_events')
            ->onDelete('cascade');
        $table->string('file_name');
        $table->string('file_path');
        $table->string('file_type')->nullable(); // pdf / docx / pptx / image
        $table->integer('file_size')->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_event_files');
    }
};

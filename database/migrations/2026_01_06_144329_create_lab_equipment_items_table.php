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
        Schema::create('lab_equipment_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('equipment_id')->constrained('equipment')->cascadeOnDelete();

            $table->foreignId('lab_id')->constrained('labs')->cascadeOnDelete();

            $table->unsignedInteger('quantity')->default(0);
            $table->unsignedInteger('broken_quantity')->default(0);
            $table->unsignedInteger('actual_quantity')->default(0);

            $table->timestamps();

            $table->unique(['lab_id', 'equipment_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_equipment_items');
    }
};

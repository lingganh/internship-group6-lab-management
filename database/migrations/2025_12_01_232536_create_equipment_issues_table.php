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
        Schema::create('equipment_issues', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('equipment_id');

            $table->foreignId('reported_by')->constrained('users');

            $table->string('title');
            $table->text('description');

            $table->json('images')->nullable()->comment('Array of image paths');

            $table->enum('status', ['pending', 'in_progress', 'resolved', 'closed'])->default('pending');

            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');

            $table->foreignId('assigned_to')->nullable()->constrained('users');

            $table->timestamp('resolved_at')->nullable();
            $table->text('resolution_notes')->nullable();

            $table->timestamps();

            $table->index(['equipment_id', 'status']);
            $table->index(['assigned_to', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_issues');
    }
};

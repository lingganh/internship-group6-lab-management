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
        Schema::create('equipment_issue_logs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('equipment_issue_id');
            $table->unsignedBigInteger('changed_by'); // user cập nhật

            // Trạng thái cũ / mới
            $table->string('from_status')->nullable();
            $table->string('to_status')->nullable();

            // Ưu tiên cũ / mới
            $table->string('from_priority')->nullable();
            $table->string('to_priority')->nullable();

            // Ghi chú xử lý tại thời điểm đó
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('equipment_issue_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_issue_logs');
    }
};

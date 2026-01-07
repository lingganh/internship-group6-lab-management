<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment_issue_requests', function (Blueprint $table) {
            $table->id();

            // Người tạo phiếu
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Tiêu đề chung của phiếu
            $table->string('title');

            // Mô tả chung 
            $table->text('description')->nullable();

            // Trạng thái phiếu
            $table->string('status')
                ->default('pending')
                ->comment('pending, in_review, completed, cancelled');

            // Tổng số item 
            $table->unsignedInteger('items_count')->default(0);

            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment_issue_requests');
    }
};

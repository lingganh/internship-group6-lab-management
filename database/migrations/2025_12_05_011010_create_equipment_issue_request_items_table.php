<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment_issue_request_items', function (Blueprint $table) {
            $table->id();

            // FK tới phiếu tổng
            $table->foreignId('request_id')
                ->constrained('equipment_issue_requests')
                ->cascadeOnDelete();

            // FK tạm tới test_equipments (sau này sẽ đổi sang equipment)
            $table->foreignId('equipment_id')
                ->constrained('equipment')
                ->cascadeOnDelete();

            // Tiêu đề / mô tả riêng cho thiết bị này
            $table->string('title');
            $table->text('description')->nullable();

            // Lưu tối đa 2 ảnh – mảng path
            $table->json('images')->nullable();

            // Trạng thái xử lý item
            $table->string('status')
                ->default('pending')
                ->comment('pending, approved, rejected');

            $table->timestamps();

            $table->index(['request_id', 'status']);
            $table->index(['equipment_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment_issue_request_items');
    }
};

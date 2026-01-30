<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Bảng loại sự kiện (category) - có icon
        Schema::create('event_categories', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique(); // work, seminar, other
            $table->string('name', 100); // Công việc, Hội thảo
            $table->string('icon', 50)->default('calendar'); // briefcase, clock
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Bảng trạng thái sự kiện (status) - có màu
        Schema::create('event_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique(); // pending, approved, cancelled
            $table->string('name', 100); // Chờ duyệt, Đã duyệt
            $table->string('color', 20)->default('#cccccc'); // #ff0000
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert data mẫu
        DB::table('event_categories')->insert([
            ['code' => 'work', 'name' => 'Công việc', 'icon' => 'briefcase', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'seminar', 'name' => 'Hội thảo', 'icon' => 'clock', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'other', 'name' => 'Khác', 'icon' => 'question-circle', 'created_at' => now(), 'updated_at' => now()],
 
        ]);

        DB::table('event_statuses')->insert([
            ['code' => 'pending', 'name' => 'Chờ duyệt', 'color' => '#ffa500', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'approved', 'name' => 'Đã duyệt', 'color' => '#00ff00', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'cancelled', 'name' => 'Đã hủy', 'color' => '#ff0000', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'completed', 'name' => 'Đã hoàn thành', 'color' => '#0000ff', 'created_at' => now(), 'updated_at' => now()],

        ]);
    }

    public function down()
    {
        Schema::dropIfExists('event_statuses');
        Schema::dropIfExists('event_categories');
    }
};
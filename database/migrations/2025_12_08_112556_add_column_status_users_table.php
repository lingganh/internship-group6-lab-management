<?php

use App\Enums\UserStatus;
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
        Schema::table('users', function (Blueprint $table) {
            $table->string('status')
                ->after('email_verified_at')
                ->default(UserStatus::Pending->value);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('class_name'); // xóa cột string cũ
            $table->unsignedBigInteger('department_id')
                ->after('gender')
                ->nullable(); // tạo cột int mới
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'status',
                'department_id',
            ]);
            $table->string('class_name')->nullable();
        });
    }
};

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
        Schema::table('equipment', function (Blueprint $table) {
            $table->unsignedInteger('quantity')->default(0);
            $table->unsignedInteger('broken_quantity')->default(0);
            $table->unsignedInteger('actual_quantity')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('equipment', function (Blueprint $table) {
            $table->dropColumn([
                'quantity',
                'broken_quantity',
                'actual_quantity'
            ]);
        });
    }
};

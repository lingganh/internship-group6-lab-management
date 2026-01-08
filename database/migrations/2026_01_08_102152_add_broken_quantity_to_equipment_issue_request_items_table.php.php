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
        Schema::table('equipment_issue_request_items', function (Blueprint $table) {
            if (!Schema::hasColumn('equipment_issue_request_items', 'broken_quantity')) {
                $table->unsignedInteger('broken_quantity')->default(1)->after('equipment_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('equipment_issue_request_items', function (Blueprint $table) {
            if (Schema::hasColumn('equipment_issue_request_items', 'broken_quantity')) {
                $table->dropColumn('broken_quantity');
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('equipment_issue_requests', function (Blueprint $table) {
            if (Schema::hasColumn('equipment_issue_requests', 'title')) {
                $table->dropColumn('title');
            }
        });

        Schema::table('equipment_issue_request_items', function (Blueprint $table) {
            if (Schema::hasColumn('equipment_issue_request_items', 'title')) {
                $table->dropColumn('title');
            }
        });
    }

    public function down(): void
    {
        Schema::table('equipment_issue_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('equipment_issue_requests', 'title')) {
                $table->string('title')->nullable();
            }
        });

        Schema::table('equipment_issue_request_items', function (Blueprint $table) {
            if (!Schema::hasColumn('equipment_issue_request_items', 'title')) {
                $table->string('title')->nullable();
            }
        });
    }
};

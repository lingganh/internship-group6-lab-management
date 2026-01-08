<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('equipment_issue_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('equipment_issue_requests', 'lab_event_id')) {
                return;
            }

            // unique cho lab_event_id
            $table->unique('lab_event_id', 'eir_unique_lab_event_id');
        });
    }

    public function down(): void
    {
        Schema::table('equipment_issue_requests', function (Blueprint $table) {
            $table->dropUnique('eir_unique_lab_event_id');
        });
    }
};

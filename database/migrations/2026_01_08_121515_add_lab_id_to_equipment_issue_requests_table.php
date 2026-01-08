<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('equipment_issue_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('equipment_issue_requests', 'lab_id')) {
                $table->foreignId('lab_id')->nullable()->constrained('labs')->nullOnDelete()->after('lab_event_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('equipment_issue_requests', function (Blueprint $table) {
            if (Schema::hasColumn('equipment_issue_requests', 'lab_id')) {
                $table->dropConstrainedForeignId('lab_id');
            }
        });
    }
};

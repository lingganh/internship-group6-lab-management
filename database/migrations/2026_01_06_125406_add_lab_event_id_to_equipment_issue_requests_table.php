<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('equipment_issue_requests', function (Blueprint $table) {
            $table->foreignId('lab_event_id')
                ->nullable()
                ->after('user_id')
                ->constrained('lab_events')
                ->nullOnDelete();

            $table->index('lab_event_id');
        });
    }

    public function down(): void
    {
        Schema::table('equipment_issue_requests', function (Blueprint $table) {
            $table->dropForeign(['lab_event_id']);
            $table->dropIndex(['lab_event_id']);
            $table->dropColumn('lab_event_id');
        });
    }
};

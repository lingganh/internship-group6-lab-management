<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('equipment_issue_request_items', function (Blueprint $table) {
            $table->unsignedBigInteger('equipment_issue_id')
                ->nullable()
                ->after('equipment_id');

            $table->foreign('equipment_issue_id')
                ->references('id')
                ->on('equipment_issues')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('equipment_issue_request_items', function (Blueprint $table) {
            $table->dropForeign(['equipment_issue_id']);
            $table->dropColumn('equipment_issue_id');
        });
    }
};

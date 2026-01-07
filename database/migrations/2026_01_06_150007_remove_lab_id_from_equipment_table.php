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
            try {
                $table->dropForeign(['lab_id']);
            } catch (\Exception $e) {}

            try {
                $table->dropIndex('equipment_lab_id_status_index');
            } catch (\Exception $e) {}

            $table->dropColumn('lab_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('equipment', function (Blueprint $table) {
            //
        });
    }
};

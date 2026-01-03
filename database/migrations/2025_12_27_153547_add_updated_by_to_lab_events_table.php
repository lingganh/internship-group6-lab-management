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
        Schema::table('lab_events', function (Blueprint $table) {
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('lab_code');
            $table->foreign('lab_code')->references('code')->on('labs')->cascadeOnDelete(); 
            $table->string('color', 20)->nullable()->after('category');

           });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lab_events', function (Blueprint $table) {
            //
        });
    }
};

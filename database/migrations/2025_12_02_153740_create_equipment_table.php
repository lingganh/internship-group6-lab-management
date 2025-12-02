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
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId("lab_id")->constrained("labs")->cascadeOnDelete();
            $table->string("name");
            $table->string("code")->unique();
            $table->string("type");
            $table->enum("status", ["available", "in_use", "maintenance", "broken"])->default("available");
            $table->date( " purchased_date")->nullable();
            $table->json("specifications")->nullable();
            $table->text("notes")->nullable();
            $table->timestamps();

            //indexes
            $table->index(['lab_id', 'status']);
            $table->index('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};

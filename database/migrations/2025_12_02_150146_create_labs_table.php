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
        Schema::create('labs', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("code")->unique();
            $table->string("location")->nullable();
            $table->unsignedSmallInteger("capacity")->nullable();
            $table->text("description")->nullable();
            $table->json("facilities")->nullable();
            $table->enum("status", ["active", "maintenance", "locked"])->default("active");
            $table->string("image_url")->nullable();
            $table->foreignId("created_by")->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('labs');
    }
};

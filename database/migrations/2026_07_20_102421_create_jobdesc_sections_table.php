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
        Schema::create('jobdesc_sections', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 12)->unique();
            $table->string('name');
            $table->string('file')->nullable();
            $table->foreignId('section_id')->constrained('sections')->onDelete('cascade');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobdesc_sections');
    }
};

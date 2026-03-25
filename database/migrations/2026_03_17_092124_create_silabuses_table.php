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
        Schema::create('silabuses', function (Blueprint $table) {
            $table->id();
            $table->string('code_module');
            $table->string('duration');
            $table->text('description');
            $table->json('target');
            $table->json('qualification');
            $table->foreignId('materi_id')->constrained('materis')->onDelete('cascade');
            $table->foreignId('workshop_id')->constrained('workshops')->onDelete('cascade');
            $table->json('pic');
            $table->json('grading');
            $table->foreignId('user')->constrained('users')->onDelete('cascade');
            $table->foreignId('manager')->constrained('users')->onDelete('cascade');
            $table->foreignId('director')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('silabuses');
    }
};

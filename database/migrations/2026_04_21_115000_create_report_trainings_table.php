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
        Schema::create('report_trainings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->string('category_training');
            $table->text('description');
            $table->text('materi');
            $table->text('targets');
            $table->text('notes');
            $table->dateTime('report_date');
            $table->foreignId('manager')->constrained('users')->onDelete('cascade')->nullable();
            $table->foreignId('director')->constrained('users')->onDelete('cascade')->nullable();
            $table->string('director_name');
            $table->string('director_signature')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_trainings');
    }
};

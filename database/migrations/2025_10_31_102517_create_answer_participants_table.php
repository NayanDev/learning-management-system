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
        Schema::create('answer_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->nullable()->constrained('events');
            $table->string('type')->nullable();
            $table->foreignId('participant_id')->nullable()->constrained('participants');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->foreignId('question_id')->nullable()->constrained('questions');
            $table->foreignId('answer_id')->nullable()->constrained('answers');
            $table->integer('point');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('answer_participants');
    }
};

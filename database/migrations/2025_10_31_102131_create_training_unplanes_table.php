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
        Schema::create('training_unplanes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('workshop_id')->constrained()->onDelete('cascade');
            $table->string('organizer');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->string('divisi');
            $table->enum('instructor', ['internal', 'external']);
            $table->string('location');
            $table->enum('status', ['open', 'submit', 'approve', 'close', 'reject'])->default('open');
            $table->text('notes')->nullable();
            $table->foreignId('approve_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_unplanes');
    }
};

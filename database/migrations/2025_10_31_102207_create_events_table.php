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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workshop_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('divisi');
            $table->string('year');
            $table->string('letter_number')->nullable();
            $table->string('organizer')->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->string('token');
            $table->string('token_expired');
            $table->enum('instructor', ['internal', 'external']);
            $table->string('location')->nullable();
            $table->foreignId('approve_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('created_date')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['open', 'submit', 'approve', 'close', 'reject'])->default('open');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};

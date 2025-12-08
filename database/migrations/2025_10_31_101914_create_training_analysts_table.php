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
        Schema::create('training_analysts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_id')->constrained()->onDelete('cascade');
            $table->json('qualification')->nullable();      // array text
            $table->json('general')->nullable();    // array text
            $table->json('technic')->nullable();    // array text
            $table->enum('status', ['open', 'submit', 'approve', 'close', 'reject'])->default('open');
            $table->text('notes')->nullable();
            $table->dateTime('created_date')->nullable();
            $table->foreignId('approve_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('divisi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_analysts');
    }
};

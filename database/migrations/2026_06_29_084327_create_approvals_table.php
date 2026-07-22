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
        Schema::create('approvals', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            // Polymorphic
            $table->string('approval_type');
            $table->unsignedBigInteger('approval_id');

            // Approver berdasarkan user
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users');

            $table->dateTime('date')->nullable();
            $table->timestamps();

            $table->index(['approval_type', 'approval_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approvals');
    }
};

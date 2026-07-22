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
        Schema::create('director_signatures', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('divisi');
            $table->string('position');
            $table->string('signature');
            $table->dateTime('date')->nullable();
            // Polymorphic
            $table->string('approval_type');
            $table->unsignedBigInteger('approval_id');
            $table->index(['approval_type', 'approval_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('director_signatures');
    }
};

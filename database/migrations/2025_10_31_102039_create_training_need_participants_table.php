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
        Schema::create('training_need_participants', function (Blueprint $table) {
            $table->id();
            $table->string('company');
            $table->string('nik');
            $table->string('name');
            $table->string('divisi');
            $table->string('unit_kerja');
            $table->string('status');
            $table->string('jk');
            $table->string('email');
            $table->string('telp');
            $table->foreignId('training_need_workshop_id')->constrained('training_need_workshops')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_need_participants');
    }
};

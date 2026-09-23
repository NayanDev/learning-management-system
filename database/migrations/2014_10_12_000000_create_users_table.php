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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('company');
            $table->string('divisi');
            $table->string('unit_kerja')->nullable();
            $table->string('status');
            $table->string('jk');
            $table->string('telp');
            $table->string('nik')->unique()->nullable();
            $table->string('signature')->nullable();
            $table->unsignedBigInteger('role_id');
            $table->string('qualification')->nullable();
            $table->string('position')->nullable();
            $table->tinyInteger('is_trainer')->default(0);
            $table->tinyInteger('is_leader')->default(0);
            $table->string('password');
            $table->timestamps();

            $table->foreign('role_id')->references('id')->on('roles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

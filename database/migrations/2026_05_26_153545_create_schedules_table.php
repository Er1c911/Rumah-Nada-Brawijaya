<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {

            $table->id();

            $table->string('nama');

            $table->date('tanggal');

            $table->string('jam_mulai');

            $table->string('jam_selesai');

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
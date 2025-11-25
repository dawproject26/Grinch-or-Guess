<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->nullable();
            $table->integer('seconds')->default(120);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timers');
    }
};
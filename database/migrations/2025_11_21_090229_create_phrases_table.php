<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('phrases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('panel_id')->constrained('panels')->onDelete('cascade');
            $table->text('phrase');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('phrases');
    }
};
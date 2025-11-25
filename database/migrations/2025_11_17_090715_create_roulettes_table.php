<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
<<<<<<< HEAD
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roulettes', function (Blueprint $table) {
            $table->id();
            $table->integer('vocal');
            $table->integer('consonant');
            $table->integer('letter');
            $table->integer('demogorgon');
            $table->integer('demodog');
            $table->integer('vecna');
            $table->integer('eleven');
            $table->integer('option');
=======
    public function up(): void
    {
        Schema::create('roulette', function (Blueprint $table) {
            $table->id();
            $table->string('option');
>>>>>>> ed8225db0ed5e46e9eadb2935b1cf6ca9e49c762
            $table->timestamps();
        });
    }

<<<<<<< HEAD
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roulettes');
=======
    public function down(): void
    {
        Schema::dropIfExists('roulette');
>>>>>>> ed8225db0ed5e46e9eadb2935b1cf6ca9e49c762
    }
};

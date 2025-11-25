<?php

<<<<<<< HEAD
=======

>>>>>>> ed8225db0ed5e46e9eadb2935b1cf6ca9e49c762
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

<<<<<<< HEAD
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('scores', function (Blueprint $table) {
            $table->id();
            $table->integer('points');
            $table->integer('score');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scores');
    }
};
=======

return new class extends Migration
{
public function up(): void
{
    Schema::create('scores', function (Blueprint $table) {
    $table->id();
    $table->foreignId('player_id')->constrained('players')->onDelete('cascade');
    $table->integer('score')->default(0);
    $table->timestamps();
    });
}


public function down(): void
    {
    Schema::dropIfExists('scores');
    }
};
>>>>>>> ed8225db0ed5e46e9eadb2935b1cf6ca9e49c762

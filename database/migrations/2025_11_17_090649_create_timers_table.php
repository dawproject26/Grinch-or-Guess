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
        Schema::create('timers', function (Blueprint $table) {
            $table->id();
            $table->timestamp('time');
            $table->integer('fk_option');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timers');
    }
};
=======

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
>>>>>>> ed8225db0ed5e46e9eadb2935b1cf6ca9e49c762

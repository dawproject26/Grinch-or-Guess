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
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('fk_score');
            $table->integer('fk_option');
            $table->string('letter');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
=======

return new class extends Migration
{
public function up(): void
{
Schema::create('players', function (Blueprint $table) {
$table->id();
$table->string('name')->nullable();
$table->timestamps();
});
}


public function down(): void
{
Schema::dropIfExists('players');
}
};
>>>>>>> ed8225db0ed5e46e9eadb2935b1cf6ca9e49c762

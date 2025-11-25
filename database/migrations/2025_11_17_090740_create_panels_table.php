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
=======
>>>>>>> ed8225db0ed5e46e9eadb2935b1cf6ca9e49c762
    public function up(): void
    {
        Schema::create('panels', function (Blueprint $table) {
            $table->id();
<<<<<<< HEAD
            $table->text('phrases');
            $table->integer('player_score')->default(0);
            $table->string('letter', 1)->nullable();
            $table->string('state')->default('active');
            $table->integer('timer')->default(120);
=======
            $table->string('title')->nullable();
>>>>>>> ed8225db0ed5e46e9eadb2935b1cf6ca9e49c762
            $table->timestamps();
        });
    }

<<<<<<< HEAD
    /**
     * Reverse the migrations.
     */
=======
>>>>>>> ed8225db0ed5e46e9eadb2935b1cf6ca9e49c762
    public function down(): void
    {
        Schema::dropIfExists('panels');
    }
<<<<<<< HEAD
};
=======
};
>>>>>>> ed8225db0ed5e46e9eadb2935b1cf6ca9e49c762

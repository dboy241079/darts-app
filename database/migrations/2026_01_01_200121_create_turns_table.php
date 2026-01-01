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
    Schema::create('turns', function (Blueprint $table) {
        $table->id();
        $table->foreignId('game_id')->constrained()->cascadeOnDelete();

        $table->unsignedTinyInteger('seat');          // 1 oder 2
        $table->unsignedInteger('turn_no');           // fortlaufend pro Spiel
        $table->unsignedSmallInteger('score');        // 0-180

        $table->unsignedSmallInteger('remaining_before');
        $table->unsignedSmallInteger('remaining_after');

        $table->boolean('is_bust')->default(false);
        $table->boolean('is_finish')->default(false);

        $table->timestamps();

        $table->index(['game_id', 'turn_no']);
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('turns');
    }
};

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
    Schema::create('game_players', function (Blueprint $table) {
        $table->id();
        $table->foreignId('game_id')->constrained()->cascadeOnDelete();
        $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

        $table->string('display_name');
        $table->unsignedTinyInteger('seat'); // 1 oder 2
        $table->boolean('is_starting_player')->default(false);

        $table->timestamps();
        $table->unique(['game_id', 'seat']);
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_players');
    }
};

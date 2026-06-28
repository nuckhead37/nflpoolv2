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
        DB::statement("SET FOREIGN_KEY_CHECKS = 0");
        DB::statement("truncate `game_results`");
        DB::statement("truncate `picks`");
        DB::statement("truncate `games_played`");
        DB::statement("truncate `schedule`");
        DB::statement("SET FOREIGN_KEY_CHECKS = 1");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

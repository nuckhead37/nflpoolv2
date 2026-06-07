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
        Schema::create('game_times', function (Blueprint $table) {
            $table->id();
            $table->string('time');
        });

        DB::table('game_times')->insert([
            ['time' => '6pm (EST)'],
            ['time' => '9:05pm (EST)'],
            ['time' => '9:25pm (EST)'],
            ['time' => '8:20pm (EST)'],
            ['time' => '8:15pm (EST)'],
            ['time' => '7:30pm (EST)'],
            ['time' => '9:30am (EST)']
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_times');
    }
};

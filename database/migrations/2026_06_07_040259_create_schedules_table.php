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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->unsignedSmallInteger('week');
            $table->unsignedSmallInteger('home_id');
            $table->unsignedSmallInteger('away_id');
            $table->unsignedSmallInteger('game_time_id')->default('0');
            $table->unsignedSmallInteger('game_type_id')->default('0');
            $table->unsignedSmallInteger('order');
            $table->timestamps();
            $table->index('year');
            $table->index('week');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};

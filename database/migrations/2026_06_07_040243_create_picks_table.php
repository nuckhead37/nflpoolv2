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
        Schema::create('picks', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('user_id')->default(0);
            $table->unsignedSmallInteger('schedule_id')->default(0);
            $table->unsignedSmallInteger('team_id')->default(0);
            $table->unsignedSmallInteger('points')->default(0);
            $table->timestamps();
            $table->index('user_id');
            $table->index('schedule_id');
            $table->unique(['user_id', 'schedule_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('picks');
    }
};

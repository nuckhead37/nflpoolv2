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
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->year('year');
            $table->tinyInteger('week');
            $table->foreignId('user_id')->constrained();
            $table->double('score');
            $table->tinyInteger('winner')->default('0');
            $table->tinyInteger('tied')->default('0');
            $table->timestamps();
            $table->index(['year', 'week']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};

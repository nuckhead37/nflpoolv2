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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('name',50)->nullable(false);
            $table->string('value',150)->nullable();
            $table->index('name');
        });

        DB::table('settings')->insert([
            ['id' => 1, 'name' => 'current_season', 'value' => '2026'],
            ['id' => 2, 'name' => 'first_season', 'value' => '1996'],
            ['id' => 3, 'name' => 'weeks_per_season', 'value' => '18']
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};

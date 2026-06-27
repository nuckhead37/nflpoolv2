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
        Schema::create('nfl_teams', function (Blueprint $table) {
            $table->id();
            $table->string('full_name',50)->nullable(false);
            $table->string('short_name', 20)->nullable(false);
            $table->text('abbreviation',3)->nullable(false);
            $table->unsignedSmallInteger('active')->default('1');
        });

        DB::table('nfl_teams')->insert([
            ['full_name' => 'Arizona Cardinals', 'short_name' => 'Cardinals', 'abbreviation' => 'ARI', 'active' => 1],
            ['full_name' => 'Atlanta Falcons', 'short_name' => 'Falcons', 'abbreviation' => 'ATL', 'active' => 1],
            ['full_name' => 'Baltimore Ravens', 'short_name' => 'Ravens', 'abbreviation' => 'BAL', 'active' => 1],
            ['full_name' => 'Buffalo Bills', 'short_name' => 'Bills', 'abbreviation' => 'BUF', 'active' => 1],
            ['full_name' => 'Carolina Panthers', 'short_name' => 'Panthers', 'abbreviation' => 'CAR', 'active' => 1],
            ['full_name' => 'Chicago Bears', 'short_name' => 'Bears', 'abbreviation' => 'CHI', 'active' => 1],
            ['full_name' => 'Cincinnati Bengals', 'short_name' => 'Bengals', 'abbreviation' => 'CIN', 'active' => 1],
            ['full_name' => 'Cleveland Browns', 'short_name' => 'Browns', 'abbreviation' => 'CLE', 'active' => 1],
            ['full_name' => 'Dallas Cowboys', 'short_name' => 'Cowboys', 'abbreviation' => 'DAL', 'active' => 1],
            ['full_name' => 'Denver Broncos', 'short_name' => 'Broncos', 'abbreviation' => 'DEN', 'active' => 1],
            ['full_name' => 'Detroit Lions', 'short_name' => 'Lions', 'abbreviation' => 'DET', 'active' => 1],
            ['full_name' => 'Green Bay Packers', 'short_name' => 'Packers', 'abbreviation' => 'GB', 'active' => 1],
            ['full_name' => 'Houston Texans', 'short_name' => 'Texans', 'abbreviation' => 'HOU', 'active' => 1],
            ['full_name' => 'Indianapolis Colts', 'short_name' => 'Colts', 'abbreviation' => 'IND', 'active' => 1],
            ['full_name' => 'Jacksonville Jaguars', 'short_name' => 'Jaguars', 'abbreviation' => 'JAX', 'active' => 1],
            ['full_name' => 'Kansas City Chiefs', 'short_name' => 'Chiefs', 'abbreviation' => 'KC', 'active' => 1],
            ['full_name' => 'Las Vegas Raiders', 'short_name' => 'Raiders', 'abbreviation' => 'LV', 'active' => 1],
            ['full_name' => 'Los Angeles Chargers', 'short_name' => 'Chargers', 'abbreviation' => 'LAC', 'active' => 1],
            ['full_name' => 'Los Angeles Rams', 'short_name' => 'Rams', 'abbreviation' => 'LAR', 'active' => 1],
            ['full_name' => 'Miami Dolphins', 'short_name' => 'Dolphins', 'abbreviation' => 'MIA', 'active' => 1],
            ['full_name' => 'Minnesota Vikings', 'short_name' => 'Vikings', 'abbreviation' => 'MIN', 'active' => 1],
            ['full_name' => 'New England Patriots', 'short_name' => 'Patriots', 'abbreviation' => 'NE', 'active' => 1],
            ['full_name' => 'New Orleans Saints', 'short_name' => 'Saints', 'abbreviation' => 'NOS', 'active' => 1],
            ['full_name' => 'New York Giants', 'short_name' => 'Giants', 'abbreviation' => 'NYG', 'active' => 1],
            ['full_name' => 'New York Jets', 'short_name' => 'Jets', 'abbreviation' => 'NYJ', 'active' => 1],
            ['full_name' => 'Philadelphia Eagles', 'short_name' => 'Eagles', 'abbreviation' => 'PHI', 'active' => 1],
            ['full_name' => 'Pittsburgh Steelers', 'short_name' => 'Steelers', 'abbreviation' => 'PIT', 'active' => 1],
            ['full_name' => 'San Francisco 49ers', 'short_name' => '49ers', 'abbreviation' => 'SF', 'active' => 1],
            ['full_name' => 'Seattle Seahawks', 'short_name' => 'Seahawks', 'abbreviation' => 'SEA', 'active' => 1],
            ['full_name' => 'Tampa Bay Buccaneers', 'short_name' => 'Buccaneers', 'abbreviation' => 'TB', 'active' => 1],
            ['full_name' => 'Tennessee Titans', 'short_name' => 'Titans', 'abbreviation' => 'TEN', 'active' => 1],
            ['full_name' => 'Washington Commanders', 'short_name' => 'Commanders', 'abbreviation' => 'WAS', 'active' => 1]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nfl_teams');
    }
};

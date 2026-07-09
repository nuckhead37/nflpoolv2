<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stat extends Model
{
    public const HIGHEST_SEASON_SCORES = ['HighestSeasonScores' => 'Highest Season Scores'];
    public const LOWEST_SEASON_SCORES = ['LowestSeasonScores' => 'Lowest Season Scores'];
    public const HIGHEST_WEEKLY_SCORES = ['HighestWeeklyScores' => 'Highest Weekly Scores'];
    public const LOWEST_WEEKLY_SCORES = ['LowestWeeklyScores' => 'Lowest Weekly Scores'];
    public const TOTAL_CENTURY_GAMES = ['TotalCenturyGames' => 'Total Century Games'];
    public const AVERAGE_POINTS_PER_SEASON = ['AveragePointsPerSeason' => 'Average Points Per Season'];
    public const TOTAL_WINS = ['TotalWins' => 'Total Wins'];
    public const WEEKS_WON_PER_SEASON = ['WeeksWonPerSeason' => 'Weeks Won Per Season'];
    public const AVERAGE_WEEKS_WON_PER_SEASON = ['AverageWeeksWonPerSeason' => 'Average Weeks Won Per Season'];
    public const TOTAL_SEASON_POINTS = ['TotalSeasonPoints' => 'Total Season Points'];
}

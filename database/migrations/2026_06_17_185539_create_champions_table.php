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
        Schema::create('champions', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('user_id');
            $table->year('year');
            $table->timestamps();
            $table->index('user_id');
        });

        DB::statement("
        INSERT INTO `champions` (`user_id`, `year`) VALUES
            (2, 1996),
            (1, 1997),
            (2, 1998),
            (2, 1999),
            (2, 2000),
            (2, 2001),
            (2, 2002),
            (2, 2003),
            (2, 2004),
            (2, 2005),
            (2, 2006),
            (2, 2007),
            (2, 2008),
            (2, 2009),
            (2, 2010),
            (2, 2011),
            (2, 2012),
            (2, 2013),
            (2, 2014),
            (2, 2015),
            (2, 2016),
            (2, 2017),
            (2, 2018),
            (1, 2019),
            (2, 2020),
            (1, 2021),
            (1, 2022),
            (2, 2023),
            (2, 2024),
            (1, 2025)
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('champions');
    }
};

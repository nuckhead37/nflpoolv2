@include('partials/header')

<div style='margin-bottom:10px;'>
    <div id="current-season-page-header">
        <h2>{{ $currentSeason }} Season</h2>
    </div>
    @if ($seasonInAction && !empty($weekResults)) 
        <div id="current-season-grid-table">
            <div class="cell header">Week</div>
            <div class="cell header">Player</div>
            <div class="cell header">Score</div>

            <div class="cell header desktop-only">Wins</div>
            <div class="cell header desktop-only">Tied</div>
            <div class="cell header desktop-only">Leader</div>


            <!-- New -->
            <div class="cell header mobile-only">Record</div>
            <div class="cell header">Total</div>

            <div class="cell header"></div>
            @foreach ($weekResults as $key => $weeks)
                @foreach ($weeks as $weekKey => $week)
                    <!-- Column 1 -->
                    <div class="cell span-2 week-title">{{ $week['week'] }}</div>

                    <!-- Columns 2-7 (top row) -->
                    <div class="cell leader player-name">
                        <span>{{ $week['users'][0]['name'] }}</span>

                        @if($week['totals'][0]['class'] == 'total-leader')
                            <span class="leader-label">(Leader)</span>
                        @endif
                    </div>
                    <div class="cell leader">{{ $week['users'][0]['points'] }}</div>
                    <div class="cell leader desktop-only">{{ $week['totals'][0]['wins'] }}</div>

                    <div class="cell leader desktop-only">{{ $week['totals'][0]['tied'] }}</div>

                    <div class="cell {{ $week['totals'][0]['class'] }} desktop-only">
                        {{ $week['totals'][0]['name'] }}
                    </div>

                    <div class="cell mobile-only leader">
                        {{ $week['totals'][0]['wins'] }}-{{ $week['totals'][0]['tied'] }}
                    </div>

                    <div class="cell {{ $week['totals'][0]['class'] }}">
                        {{ $week['totals'][0]['total'] }}
                    </div>

                    <!-- Column 8 -->
                    <div class="cell span-2 no-right-border picks"><a href='picks/view/{{ $week["week"] }}'>Picks</a></div>

                    <!-- Columns 2-7 (bottom row) -->
                    <div class="cell player-name">
                        <span>{{ $week['users'][1]['name'] }}</span>

                        @if($week['totals'][1]['class'] == 'total-leader')
                            <span class="leader-label">(Leader)</span>
                        @endif
                    </div>
                    <div class="cell">{{ $week['users'][1]['points'] }}</div>
                    <div class="cell leader desktop-only">{{ $week['totals'][0]['wins'] }}</div>

                    <div class="cell leader desktop-only">{{ $week['totals'][0]['tied'] }}</div>

                    <div class="cell {{ $week['totals'][1]['class'] }} desktop-only">
                        {{ $week['totals'][1]['name'] }}
                    </div>

                    <div class="cell mobile-only leader">
                        {{ $week['totals'][1]['wins'] }}-{{ $week['totals'][1]['tied'] }}
                    </div>

                    <div class="cell {{ $week['totals'][1]['class'] }}">
                        {{ $week['totals'][1]['total'] }}
                    </div>
                    <div class="blank-row"></div>
                @endforeach
            @endforeach
        </div>
    @else
        <div id="current-season-no-results-table">
            <h2>No weeks played yet</h2>
        </div>
    @endif

    @if ($seasonInAction && $userLoggedIn)
            @include('partials/picks_options')
    @endif
</div>

@include('partials/footer')
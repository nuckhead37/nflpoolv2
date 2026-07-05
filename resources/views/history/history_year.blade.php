@include('partials/header')

<div style='margin-bottom:10px;'>
    <div id='history-year-page-header'>
        <a href="/history" class="back-button">
            &#8592; Back
        </a>
        <h2>Results For {{ $year }}</h2>
        <div class="header-spacer"></div>
    </div>

    <div id="history-season-grid-table">
            <div class="cell header">Week</div>
            <div class="cell header">Player</div>
            <div class="cell header">Score</div>
            <div class="cell header">Wins</div>
            <div class="cell header">Tied</div>
            <div class="cell header">Leader</div>
            <div class="cell header">Total</div>
            @foreach ($weekResults as $key => $weeks)
                @foreach ($weeks as $weekKey => $week)
                    <!-- Column 1 -->
                    <div class="cell span-2 week-title">{{ $week['week'] }}</div>

                    <!-- Columns 2-7 (top row) -->
                    <div class="cell leader">{{ $week['users'][0]['name'] }}</div>
                    <div class="cell leader">{{ $week['users'][0]['points'] }}</div>
                    <div class="cell leader">{{ $week['totals'][0]['wins'] }}</div>
                    <div class="cell leader">{{ $week['totals'][0]['tied'] }}</div>
                    <div class="cell {{ $week['totals'][0]['class'] }}">{{ $week['totals'][0]['name'] }}</div>
                    <div class="cell {{ $week['totals'][0]['class'] }}">{{ $week['totals'][0]['total'] }}</div>

                    <!-- Columns 2-7 (bottom row) -->
                    <div class="cell">{{ $week['users'][1]['name'] }}</div>
                    <div class="cell">{{ $week['users'][1]['points'] }}</div>
                    <div class="cell">{{ $week['totals'][1]['wins'] }}</div>
                    <div class="cell">{{ $week['totals'][1]['tied'] }}</div>
                    <div class="cell {{ $week['totals'][1]['class'] }}">{{ $week['totals'][1]['name'] }}</div>
                    <div class="cell {{ $week['totals'][1]['class'] }}">{{ $week['totals'][1]['total'] }}</div>
                @endforeach
            @endforeach
        </div>

</div>

@include('partials/footer')
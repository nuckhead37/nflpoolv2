@include('partials/header')

<div style='margin-bottom:10px;'>
    <div id="view-picks-page-header">
        <h2>Week {{ $week }} Picks</h2>
    </div>

    <div id="view-picks-grid-table">
        <div class='row'>
            <div class="cell header">Teams</div>
            <div class="cell header">Clive</div>
            <div class="cell header">Points</div>
            <div class="cell header"></div>
            <div class="cell header">Jim</div>
            <div class="cell header">Points</div>
            <div class="cell header"></div>
        </div>
        @foreach ($games as $game)
            <div class='row'>
                <div class="cell">{{ $game['away'] }} @ {{ $game['home'] }}</div>
                @foreach ($game['users'] as $user)
                    <div class="cell">{{ $user['team'] }}</div>
                    <div class="cell">{{ $user['points'] }}</div>
                    <div class="cell">{{ $user['result'] }}</div>
                @endforeach
            </div>
        @endforeach
    </div>

</div>

@include('partials/footer')
@include('partials/header')

<div style='margin-bottom:10px;'>
    <div id="view-picks-page-header">
        <a href="{{ $backUrl }}" class="back-button">
            &#8592; Back
        </a>
        <h2>Week {{ $week }} Picks</h2>
        <div class="header-spacer"></div>
    </div>

    <div id="view-picks-grid-table">
        <div class='row'>
            <div class="cell header">Teams</div>
            <div class="cell header">Clive</div>
            <div class="cell header">
                <div class='short-points'>Pts</div>
                <div class='long-points'>Points</div>
            </div>
            <div class="cell header"></div>
            <div class="cell header">Jim</div>
            <div class="cell header">
                <div class='short-points'>Pts</div>
                <div class='long-points'>Points</div>
            </div>
            <div class="cell header"></div>
        </div>
        @foreach ($games as $game)
            <div class='row'>
                <div class="cell short-name">{{ $game['awayShort'] }} @ {{ $game['homeShort'] }}</div>
                <div class="cell long-name">{{ $game['away'] }} @ {{ $game['home'] }}</div>

                @foreach ($game['users'] as $user)
                    <div class="cell short-name">{{ $user['teamAbbreviated'] }}</div>
                    <div class="cell long-name">{{ $user['team'] }}</div>
                    <div class="cell points">{{ $user['points'] }}</div>
                    <div class="cell status-{{ $user['result'] }}"></div>
                @endforeach
            </div>
        @endforeach
    </div>

    @if ($showMakeEdit) 
    <div id="view-picks-page-manage-picks">
        <a href="/picks/{{ $week }}" class="button-link">Manage Picks</a>
    </div>
    @endif

</div>

@include('partials/footer')
<div>
    @if(!empty($seasonCurrentTotals))
        <div id="current-season-totals-container">
            <div id="current-season-totals">
                <div class="cell header">Current Totals</div>
                <div class="cell header">Total</div>
                <div class="cell header">Wins</div>
                <div class="cell header">Tied</div>
                @foreach ($seasonCurrentTotals as $totals)
                    <div class="cell name">{{ $totals['name'] }}</div>
                    <div class="cell">{{ number_format($totals['total'], 1, ".", ",") }}</div>
                    <div class="cell">{{ $totals['wins'] }}</div>
                    <div class="cell">{{ $totals['tied'] }}</div>
                @endforeach
            </div>
        </div>
    @endif


</div>
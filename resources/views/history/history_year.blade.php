@include('partials/header')

<div style='margin-bottom:10px;'>
    <h2 class='center-header'>Results For {{ $year }}</h2>


    <div class="grid-table">
        <div class="cell header">Week</div>
        <div class="cell header">Player</div>
        <div class="cell header">Score</div>
        <div class="cell header">Winner</div>


        <div class="row">
            <div class="cell">1</div>

            <div class="cell split">
                <div>John</div>
                <div>Jane</div>
            </div>

            <div class="cell split">
                <div>Mike</div>
                <div>Sarah</div>
            </div>

            <div class="cell">3 - 1</div>
            <div class="cell">John</div>
        </div>

    </div>


</div>

@include('partials/footer')
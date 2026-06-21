@include('partials/header')

<div style='text-align: center;'>
    <h2>Current Champion: <span style='color:black;'>{{ $currentChampion }}</span></h2>
</div>
<div>
    <img src="{{ asset('images/header.jpeg') }}" id="home-image" alt="NFL Pool">
</div>
<div style='margin:10px 0;'>

    @if ($seasonInAction)
    <div>
        @include('partials/current_season_scores')
    </div>
    @endif

    <div>
    @if ($userLoggedIn && $pickWeeks !== null)
        @include('partials/picks_options')
    @endif


</div>

@include('partials/footer')
@include('partials/header')

<div>

    @foreach ($games as $game)
        <div class="option-group">
            <input type="radio" id="option1-{{ $game['id'] }}" name="choice[{{ $game['id'] }}]" value="{{ $game['awayId'] }}">
            <label for="option1-{{ $game['id'] }}">{{ $game['away'] }}</label>

            <span> @ </span>
            <input type="radio" id="option2-{{ $game['id'] }}" name="choice[{{ $game['id'] }}]" value="{{ $game['homeId'] }}" checked>
            <label for="option2-{{ $game['id'] }}">{{ $game['home'] }}</label>
        </div>
    @endforeach

</div>

@include('partials/footer')
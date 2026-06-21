@include('partials/header')

<div style='margin-bottom:10px;'>Week {{ $week }} Picks</div>
<div>

    @foreach ($games as $game)
        <div class="option-group" style='margin-bottom: 5px;'>
            <input type="radio" id="option1-{{ $game['id'] }}" name="choice[{{ $game['id'] }}]" value="{{ $game['awayId'] }}">
            <label for="option1-{{ $game['id'] }}">{{ $game['away'] }}</label>

            <span> @ </span>
            <input type="radio" id="option2-{{ $game['id'] }}" name="choice[{{ $game['id'] }}]" value="{{ $game['homeId'] }}" checked>
            <label for="option2-{{ $game['id'] }}">{{ $game['home'] }}</label>

            <div class='pick-group-options'>
                @foreach ($game['picks'] as $pick)
                <div class="pick-group">
                    <input type="radio" class='pick' id="pick-{{ $pick }}-{{ $game['id'] }}" name="pick[{{ $game['id'] }}]" value="{{ $pick }}">
                    <label for="pick-{{ $pick }}-{{ $game['id'] }}" class='pick'>{{ $pick }}</label>
                </div>
                @endforeach
            </div>
        </div>
    @endforeach

</div>

@include('partials/footer')
@include('partials/header')

<div style='margin-bottom:10px;'>

    <div id='enter-results-page-header'>
        <h2>{{ $titleType }} Week {{ $week }} Results</h2>
    </div>
    @if ($error)
        <div id="enter-results-page-error">
            <span>Failed to enter results</span>
        </div>
    @endif
    <div class='results-table'>
        <form id='results-form' action="{{ route($formUrl) }}" method="post">
            @csrf
            <input type='hidden' name='week' value="{{ $week }}">
            @foreach($games as $index => $game)
                <div class="team-row" data-game="{{ $game['id'] }}">
                    {{-- Team Selection --}}
                    <div class='team-cell team-left'>
                        @php
                            $selectedTeam = $game['player']['teamId'] ?? null;
                        @endphp
                        <label class="team-btn {{ $selectedTeam == $game['awayId'] ? 'selected' : '' }}">
                            <input
                                type="radio"
                                name="games[{{ $game['id'] }}]"
                                value="{{ $game['awayId'] }}"
                                {{ $selectedTeam == $game['awayId'] ? 'checked' : '' }}
                            >
                            <span class="team-full">{{ $game['away'] }}</span>
                            <span class="team-short">{{ $game['awayShort'] }}</span>
                        </label>
                        <span class="at-sign">@</span>
                        <label class="team-btn {{ $selectedTeam == $game['homeId'] ? 'selected' : '' }}">
                            <input
                                type="radio"
                                name="games[{{ $game['id'] }}]"
                                value="{{ $game['homeId'] }}"
                                {{ $selectedTeam == $game['homeId'] ? 'checked' : '' }}
                            >
                            <span class="team-full">{{ $game['home'] }}</span>
                            <span class="team-short">{{ $game['homeShort'] }}</span>
                        </label>
                    </div>
                </div>
            @endforeach
            <div id='enter-results-button-container'>
                <button type='submit' id='enter-results-button'>Submit</button>
            </div>
        </form>
    </div>

</div>

<script>
    document.querySelectorAll('.team-btn input').forEach(input => {

        input.addEventListener('change', function () {

            const container = this.closest('.team-cell');

            container.querySelectorAll('.team-btn')
                .forEach(btn => btn.classList.remove('selected'));

            this.closest('.team-btn')
                .classList.add('selected');

        });

    });
</script>

@include('partials/footer')
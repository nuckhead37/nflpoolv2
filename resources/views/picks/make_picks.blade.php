@include('partials/header')

<div>
@foreach($games as $index => $game)
    <div class="game-row" data-game="{{ $game['id'] }}">

        {{-- Team Selection --}}
        <div class="team-column">
            @php
                $selectedTeam = $game['player']['teamId'] ?? null;
            @endphp
            <label class="team-btn {{ $selectedTeam == $game['awayId'] ? 'selected' : '' }}">
                <input
                    type="radio"
                    name="team_{{ $game['id'] }}"
                    value="{{ $game['awayId'] }}"
                    {{ $selectedTeam == $game['awayId'] ? 'checked' : '' }}
                >
                {{ $game['away'] }}
            </label>
            @
            <label class="team-btn {{ $selectedTeam == $game['homeId'] ? 'selected' : '' }}">
                <input
                    type="radio"
                    name="team_{{ $game['id'] }}"
                    value="{{ $game['homeId'] }}"
                    {{ $selectedTeam == $game['homeId'] ? 'checked' : '' }}
                >
                {{ $game['home'] }}
            </label>
        </div>

        {{-- Pick Selection --}}
        <div class="pick-column">
            @php
                $selectedPick = $game['player']['pick'] ?? 0;
            @endphp

            @foreach($game['picks'] as $pick)
                <button
                    type="button"
                    class="pick-btn {{ $selectedPick == $pick ? 'selected' : '' }}"
                    data-game="{{ $game['id'] }}"
                    data-pick="{{ $pick }}"
                >
                    {{ $pick }}
                </button>
            @endforeach
        </div>

    </div>
@endforeach
</div>


<script>
document.addEventListener('DOMContentLoaded', () => {

    function updateDisabledButtons() {

        // Reset everything first
        document.querySelectorAll('.pick-btn').forEach(btn => {
            btn.disabled = false;
            btn.classList.remove('disabled');
        });

        // Get all selected buttons
        const selectedButtons = document.querySelectorAll('.pick-btn.selected');

        // Disable all other picks in the same row
        selectedButtons.forEach(selectedBtn => {

            const row = selectedBtn.closest('.game-row');

            row.querySelectorAll('.pick-btn').forEach(btn => {

                if (btn !== selectedBtn) {
                    btn.disabled = true;
                    btn.classList.add('disabled');
                }

            });

        });

        // Disable same pick value in other rows
        selectedButtons.forEach(selectedBtn => {

            const selectedValue = selectedBtn.dataset.pick;
            const selectedRow = selectedBtn.closest('.game-row');

            document.querySelectorAll(
                `.pick-btn[data-pick="${selectedValue}"]`
            ).forEach(btn => {

                if (btn.closest('.game-row') !== selectedRow) {
                    btn.disabled = true;
                    btn.classList.add('disabled');
                }

            });

        });

    }

    // Initial page load
    updateDisabledButtons();

    // Pick button click
    document.querySelectorAll('.pick-btn').forEach(btn => {

        btn.addEventListener('click', function () {

            if (this.disabled) {
                return;
            }

            const row = this.closest('.game-row');

            // Deselect current selection in row
            const current = row.querySelector('.pick-btn.selected');

            if (current === this) {
                // Toggle off
                this.classList.remove('selected');
            } else {

                if (current) {
                    current.classList.remove('selected');
                }

                this.classList.add('selected');
            }

            updateDisabledButtons();
        });

    });

    // Team button styling
    document.querySelectorAll('.team-btn input').forEach(input => {

        input.addEventListener('change', function () {

            const container = this.closest('.team-column');

            container.querySelectorAll('.team-btn')
                .forEach(btn => btn.classList.remove('selected'));

            this.closest('.team-btn')
                .classList.add('selected');

        });

    });

});
</script>


<!-- old below -->




<!-- 
<div>

    @foreach ($games as $game)
        <div class="option-group" style='margin-bottom: 5px;'>
            <input type="radio" id="option1-{{ $game['id'] }}" name="choice[{{ $game['id'] }}]" value="{{ $game['awayId'] }}"  {{ $game['awayId'] === $game['player']['teamId'] ? ' checked' : '' }}>
            <label for="option1-{{ $game['id'] }}">{{ $game['away'] }}</label>

            <span> @ </span>
            <input type="radio" id="option2-{{ $game['id'] }}" name="choice[{{ $game['id'] }}]" value="{{ $game['homeId'] }}" {{ $game['homeId'] === $game['player']['teamId'] ? ' checked' : '' }}>
            <label for="option2-{{ $game['id'] }}">{{ $game['home'] }}</label>

            <div class='pick-group-options'>
                @foreach ($game['picks'] as $pick)
                <div class="pick-group">
                    <input type="radio" class='pick' id="pick-{{ $pick }}-{{ $game['id'] }}" name="pick[{{ $game['id'] }}]" value="{{ $pick }}" {{ $pick === $game['player']['pick'] ? ' checked' : '' }}>
                    <label for="pick-{{ $pick }}-{{ $game['id'] }}" class='pick'>{{ $pick }}</label>
                </div>
                @endforeach
            </div>
        </div>
    @endforeach

</div> -->

@include('partials/footer')
@include('partials/header')

<div>
    <div id='current-season-page-header'>
        <h2>{{ $week }} Picks</h2>
    </div>

    <div class='pick-table'>
        <form id='pick-form' action="{{ route('make-pick-form-submit') }}" method="post">
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
                                name="team_{{ $game['id'] }}"
                                value="{{ $game['awayId'] }}"
                                {{ $selectedTeam == $game['awayId'] ? 'checked' : '' }}
                            >
                            <span class="team-full">{{ $game['away'] }}</span>
                            <span class="team-short">{{ $game['awayShort'] }}</span>
                        </label>
                        &nbsp;@
                        <label class="team-btn {{ $selectedTeam == $game['homeId'] ? 'selected' : '' }}">
                            <input
                                type="radio"
                                name="team_{{ $game['id'] }}"
                                value="{{ $game['homeId'] }}"
                                {{ $selectedTeam == $game['homeId'] ? 'checked' : '' }}
                            >
                            <span class="team-full">{{ $game['home'] }}</span>
                            <span class="team-short">{{ $game['homeShort'] }}</span>
                        </label>
                    </div>
                    <div class='team-cell team-right'>
                        {{-- Pick Selection --}}
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
            <div id='make-picks-error-container'>
                <span>Make picks for all the games</span>
            </div>
            <div id='make-picks-button-container'>
                <button type='button' id='make-picks-button'>Submit</button>
            </div>
        </form>
    </div>
</div>


<script>
document.getElementById("make-picks-button").addEventListener("click", function () {
    // Perform validation
    if (!validateForm()) {
        return;
    }

    // Validation passed - submit the form
    document.getElementById("pick-form").submit();
});

const errorDiv = document.getElementById('make-picks-error-container');

function validateForm() {
    const selectedButtons = document.querySelectorAll('.pick-btn.selected');
    const totalGames = {{ $totalGames }};
    hideErrorDiv();
    if (selectedButtons !== totalGames) {
        showErrorDiv();
        return false;
    }
    return true;
}

function hideErrorDiv() {
    errorDiv.style.display = 'none';
}

function showErrorDiv() {
    errorDiv.style.display = 'flex';
}

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

            const row = selectedBtn.closest('.team-row');

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
            const selectedRow = selectedBtn.closest('.team-row');

            document.querySelectorAll(
                `.pick-btn[data-pick="${selectedValue}"]`
            ).forEach(btn => {

                if (btn.closest('.team-row') !== selectedRow) {
                    btn.disabled = true;
                    btn.classList.add('disabled');
                }

            });

        });

    }

    // Initial page load
    updateDisabledButtons();
    hideErrorDiv();

    // Pick button click
    document.querySelectorAll('.pick-btn').forEach(btn => {

        btn.addEventListener('click', function () {

            if (this.disabled) {
                return;
            }

            hideErrorDiv();

            const row = this.closest('.team-row');

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

            const container = this.closest('.team-cell');

            container.querySelectorAll('.team-btn')
                .forEach(btn => btn.classList.remove('selected'));

            this.closest('.team-btn')
                .classList.add('selected');

        });

    });

});
</script>

@include('partials/footer')
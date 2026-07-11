document.addEventListener('DOMContentLoaded', function () {
    const playerSelect = document.getElementById('player-select');
    if (playerSelect) {
        playerSelect.addEventListener('change', function () {
            if (this.value === 0) {
                return;
            }
            window.location.href = '/stats/' + this.value;
        });
    }
});
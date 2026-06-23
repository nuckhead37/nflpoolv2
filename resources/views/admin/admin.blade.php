@include('partials/header')

<div style='margin-bottom:10px;' id="admin-menu">

    <h2>Admin Options</h2>
    <div class="button-container">
        <a href="/enter-results" class="button-link {{ $inputResultDisabled }}">Input Results</a>
        <a href="/update-picks" class="button-link {{ $updatePicksDisabled }}"">Update Picks</a>
        <a href="/create-season" class="button-link {{ $createNewSeasonDisabled }}">Create New Season</a>
        <a href="/edit-settings" class="button-link">Edit Settings</a>
        <a href="/manage-users" class="button-link">Manage Users</a>
        <a href="/toggle-season-in-action" class="button-link">Toggle Season In Action</a>
    </div>

</div>

@include('partials/footer')
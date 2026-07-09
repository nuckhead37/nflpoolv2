@include('partials/header')

<div style='margin-bottom:10px;'>
    <div id="stats-page-header">
        <a href="{{ $backUrl }}" class="back-button">
            &#8592; Back
        </a>
        <h2>Stats For {{ $name }}</h2>
    </div>

    <div class="stats-page">

        <div class="stats-column">
            <div class="stats-container">
                Column 1 - Container 1
            </div>

            <div class="stats-container">
                Column 1 - Container 2
            </div>
        </div>

        <div class="stats-column">
            <div class="stats-container">
                Column 2 - Container 1
            </div>

            <div class="stats-container">
                Column 2 - Container 2
            </div>
        </div>

        <div class="stats-column">
            <div class="stats-container">
                Column 3 - Container 1
            </div>

            <div class="stats-container">
                Column 3 - Container 2
            </div>
        </div>
    </div>

</div>

@include('partials/footer')
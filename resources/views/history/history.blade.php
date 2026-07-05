@include('partials/header')

<div style='margin-bottom:10px;'>

    <div id="history-grid-container">
        @foreach ($years as $item)
            <div class="history-grid-item">
                <h3><a href="{{ $item['linkUrl'] }}">{{ $item['year'] }}</a></h3>
                <div class='history-grid-winner'>{{ $item['winner'] }}</div>
            </div>
        @endforeach
    </div>

</div>

@include('partials/footer')
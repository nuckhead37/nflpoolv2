<div class='mt-md'>

    <div id="pick-options-container">
        {{-- Header spans all 6 columns --}}
        <div id="pick-options-header">
            <h2>Manage Picks</h2>
        </div>

        @foreach(array_chunk($pickWeeks, 6) as $row)
            @php
                $row = array_pad($row, 6, null);
            @endphp

            @foreach($row as $item)
                <div class="pick-options-cell">
                    @if($item)
                        <a href="picks/{{ $item }}">{{ $item }}</a>
                    @endif
                </div>
            @endforeach
        @endforeach
    </div>

</div>
@include('partials/header')

<div style='margin-bottom:10px;'>
    <div id="stats-page-header">
        <h2>Stats</h2>
    </div>


    <div class="stats-page">

        <div class="stats-column">
            @foreach ($column1 as $column)
            <div class="stats-container">
                <div class="stat-sub-header">
                    {{ $column['title'] }}
                </div>
                <div class="stat-body">
                    <div class="stat-row">
                        @if (count($column['data']) === 0)
                            <div class='stat-row-text'>No stats data available</div>
                        @else 
                            @foreach ($column['data'] as $stat)
                                <div class="stat-row-text">
                                    @if ($stat['columns'] === 2)
                                        <div class='stat-col-2'>{{ $stat['name'] }}</div>
                                        <div class='stat-col-2'>{{ $stat['total'] }}</div>
                                    @else
                                        <div class='stat-col-3'>{{ $stat['name'] }}</div>
                                        <div class='stat-col-3'>{{ $stat['total'] }}</div>
                                        <div class='stat-col-3'>{{ $stat['year'] }}</div>
                                    @endif
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="stats-column">
            @foreach ($column2 as $column)
            <div class="stats-container">
                <div class="stat-sub-header">
                    {{ $column['title'] }}
                </div>
                <div class="stat-body">
                    <div class="stat-row">
                        @if (count($column['data']) === 0)
                            <div class='stat-row-text'>No stats data available</div>
                        @else 
                            @foreach ($column['data'] as $stat)
                                <div class="stat-row-text">
                                    @if ($stat['columns'] === 2)
                                        <div class='stat-col-2'>{{ $stat['name'] }}</div>
                                        <div class='stat-col-2'>{{ $stat['total'] }}</div>
                                    @else
                                        <div class='stat-col-3'>{{ $stat['name'] }}</div>
                                        <div class='stat-col-3'>{{ $stat['total'] }}</div>
                                        <div class='stat-col-3'>{{ $stat['year'] }}</div>
                                    @endif
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="stats-column">
            @foreach ($column3 as $column)
            <div class="stats-container">
                <div class="stat-sub-header">
                    {{ $column['title'] }}
                </div>
                <div class="stat-body">
                    <div class="stat-row">
                        @if (count($column['data']) === 0)
                            <div class='stat-row-text'>No stats data available</div>
                        @else 
                            @foreach ($column['data'] as $stat)
                                <div class="stat-row-text">
                                    @if ($stat['columns'] === 2)
                                        <div class='stat-col-2'>{{ $stat['name'] }}</div>
                                        <div class='stat-col-2'>{{ $stat['total'] }}</div>
                                    @else
                                        <div class='stat-col-3'>{{ $stat['name'] }}</div>
                                        <div class='stat-col-3'>{{ $stat['total'] }}</div>
                                        <div class='stat-col-3'>{{ $stat['year'] }}</div>
                                    @endif
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

    </div>

</div>

@include('partials/footer')
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>NFL Pool</title>
        <link rel="stylesheet" type="text/css" href="{{ asset('css/picks.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('css/main.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('css/login.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('css/account.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('css/history.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('css/admin.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('css/current-season-totals.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('css/pick-options.css') }}">
        <link rel="icon" href="{{ asset('favicon.png') }}" type="image/x-icon">
        <script src="{{ asset('scripts/jquery4.0.min.js') }}"></script>
    </head>
    <body>
        <div id='main-container'>
            <div id='header-container'>
                <div id="header-links">
                    <span><a href='/'>Home</a></span>
                    <span><a href='/current'>Current Season</a></span>
                    <span><a href='/history'>Season History</a></span>
                    <span><a href=''>Stats</a></span>
                    @can('use admin')
                        <span><a href='/admin'>Admin</a></span>
                    @endcan
                    @if (Auth::user())
                        <span><a href='/account'>{{ $user->name }}</a></span>
                        <span><a href='/logout'>Logout</a></span>
                    @else
                        <span><a href='/login'>Login</a></span>
                    @endif
                </div>
            </div>
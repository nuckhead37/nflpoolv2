@include('partials/header')

<div class="login-container">
    <h2>Login</h2>

    @if(session('error'))
        <p class="error">{{ session('error') }}</p>
    @endif

    <form method="POST" action="{{ route('login.submit') }}">
        @csrf

        <input
            type="text"
            name="name"
            placeholder="name"
            required
        >

        <input
            type="password"
            name="password"
            placeholder="Password"
            required
        >

        <button type="submit">Login</button>
    </form>
</div>

@include('partials/footer')
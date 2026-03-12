<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body>
    <form method="POST" action="{{ route('login.post') }}">
        @csrf
        <div class="login-wrapper">
            <div class="logo-area">
                <img src="{{ asset('images/Logo SIG.png') }}">
                <img src="{{ asset('images/LOGO PH.png') }}">
            </div>

            <div class="login-card">
                <h1>LOGIN</h1>
                <p>Please Sign In to continue.</p>
                <img src="{{ asset('images/verified.png') }}">

                @if ($errors->any())
                    <div style="color:red; font-size:13px; margin-bottom:10px; text-align:center;">
                        {{ $errors->first() }}
                    </div>
                @endif

                @if(session('success'))
                    <div style="color:green; font-size:13px; margin-bottom:10px; text-align:center;">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="input-group">
                    <i data-feather="user"></i>
                    <input type="text" placeholder="Username" name="username" value="{{ old('username') }}" required />
                </div>
                <div class="input-group">
                    <i data-feather="lock"></i>
                    <input type="password" placeholder="Password" name="password" required />
                </div>
                <button type="submit">LOGIN</button>
            </div>
        </div>
    </form>

    <script>feather.replace();</script>
    {{-- login.js DIHAPUS karena sudah pakai form POST biasa --}}
</body>
</html>
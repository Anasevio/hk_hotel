<!DOCTYPE html>
<html>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <title>Login</title>
    <!-- font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <!-- css -->
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <!-- icon -->
       <script src="https://unpkg.com/feather-icons"></script>
</head>
<body>

    @if(session('success'))
    <div class="alert-success">
        {{ session('success') }}
    </div>
    @endif

    <form id="loginForm">
        @csrf
        <div class="login-wrapper">
        <div class="logo-area">
            <img src="{{ asset('images/Logo SIG.png') }}">
            <img src="{{ asset('images/LOGO PH.png') }}">
        </div>

    @if(session('success'))
<script>
    setTimeout(function() {
        const alertBox = document.querySelector('.alert-success');
        if (alertBox) {
            alertBox.style.display = 'none';
        }
    }, 3000);
</script>
@endif


        <div class="login-card">
            <h1>LOGIN</h1>
            <p>Please Sign In to continue.</p> 
            <img src="{{ asset('images/verified.png') }}">

                    <div class="input-group">
                        <i data-feather="user"></i>
                        <input
                            type="text"
                            placeholder="Username"
                            name="username"
                        />
                    </div>
                    <div class="input-group">
                        <i data-feather="lock"></i>
                        <input
                            type="password"
                            placeholder="Password"
                            name="password"
                        />
                    </div>
                    <button type="submit">LOGIN</button>
                </div>
            </div>
        </form>

        <!-- icon -->
        <script>
            feather.replace();
        </script>
        <!-- js -->
        <script src="{{ asset('js/login.js') }}"></script>
    </body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <!-- css -->
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <!-- feather icons -->
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body>
    <div class="login-wrapper">
        <!-- Logo area -->
        <div class="logo-area">
            <img src="{{ asset('images/Logo SIG.png') }}" alt="Logo SIG">
            <img src="{{ asset('images/LOGO PH.png') }}" alt="Logo PH">
        </div>

        <!-- Card Login -->
        <form action="{{ route('login') }}" method="POST" class="login-card">
            @csrf
            <h2>LOGIN</h2>
            <p>Please Sign In to continue.</p>
            <img src="{{ asset('images/verified.png') }}" alt="Verified">

            <div class="input-group">
                <i data-feather="user"></i>
                <input type="text" placeholder="Username" name="username" required>
            </div>

            <div class="input-group">
                <i data-feather="lock"></i>
                <input type="password" placeholder="Password" name="password" required>
            </div>

            <button type="submit">LOGIN</button>
        </form>
    </div>

    <script>
        feather.replace();
    </script>
</body>
</html>

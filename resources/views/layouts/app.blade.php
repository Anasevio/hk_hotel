<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Hotel')</title>

    <!-- CSS Global -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <!-- Feather Icon -->
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body>

    @yield('content')

<script>
    feather.replace();
</script>

</body>
</html>

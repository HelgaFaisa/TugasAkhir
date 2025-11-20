<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Login') | SIM Santri</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        /* Gaya dasar untuk halaman auth/login */
        body.auth-page {
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .auth-container {
            width: 100%;
            max-width: 400px;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .auth-header {
            text-align: center;
            margin-bottom: 25px;
            color: var(--text-color);
        }
        .auth-header h2 {
            margin: 0;
            font-size: 1.8rem;
        }
        .auth-header p {
            color: #777;
            margin-top: 5px;
            font-size: 0.9rem;
        }
        .btn-full {
            width: 100%;
        }
    </style>
</head>
<body class="auth-page">
    <div class="auth-container">
        @yield('auth-content')
    </div>
</body>
</html>
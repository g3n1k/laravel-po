<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Styles -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body {
                font-family: 'Figtree', sans-serif;
                background-color: #f8f9fa;
            }
            .login-container {
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .login-card {
                width: 100%;
                max-width: 400px;
            }
        </style>
    </head>
    <body class="bg-light">
        <div class="login-container">
            <div class="card login-card shadow">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <h3>{{ config('app.name', 'Laravel') }}</h3>
                        <p class="text-muted">Silakan login untuk melanjutkan</p>
                    </div>
                    {{ $slot }}
                </div>
            </div>
        </div>

        <!-- Scripts -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>

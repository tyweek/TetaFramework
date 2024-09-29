<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="/assets/css/login.css">
</head>
<body>
    <div class="container">
        <div class="login-form">
            <h2>{{ lang.login_title }}</h2>
             <!-- Mostrar errores aquí -->
             
             @if( {{ is_array($errors) }} == true)
                <ul class="errors">
                    {{ @foreach ($errors as $field => $message) }}
                        <li>{{ $message }}</li>
                    {{ @endforeach }}
                </ul>
            @endif
            @if( {{ is_array($credentials) }} == true)
                <ul class="errors">
                    {{ @foreach ($credentials as $message) }}
                            <li>{{ $message }}</li>
                    {{ @endforeach }}
                </ul>
            @endif
            <form method="POST" action="/login">
                <div class="input-group">
                    <label for="username">{{ lang.login_email }}</label>
                    <input type="email" id="username" name="username" required value="{{ username }}">
                </div>
                <div class="input-group">
                    <label for="password">{{ lang.login_password }}</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit">{{ lang.login_enter }}</button>
            </form>
        </div>
    </div>
</body>
</html>

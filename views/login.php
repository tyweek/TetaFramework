<!-- loginForm.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
</head>
<body>
    <h2>Iniciar Sesión</h2>
    <form action="/login" method="post">
        <!-- Aquí irían los campos de usuario y contraseña -->
        <input type="text" name="email" placeholder="email">
        <input type="password" name="password" placeholder="Contraseña">
        <button type="submit">Iniciar Sesión</button>
    </form>
</body>
</html>

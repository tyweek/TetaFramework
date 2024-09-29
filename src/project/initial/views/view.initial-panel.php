<!-- views/user_panel.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Usuario</title>
    <link rel="stylesheet" href="/assets/css/panel.css">
</head>
<body>
    <nav>
        <div class="container">
            <div class="logo">Panel de Usuario</div>
            <div class="navbar-links">
                <span>Bienvenido, {{ user.name }} </span>
                <a href="/logout" class="btn">{{ lang.logout }}</a>
            </div>
            <button class="toggle-btn" id="toggle-btn">☰</button>
        </div>
    </nav>

    <div class="sidebar" id="sidebar">
        <ul>
            <li><a href="#profile">Perfil</a></li>
            <li><a href="#settings">Configuración</a></li>
            <li><a href="#messages">Mensajes</a></li>
            <li><a href="#notifications">Notificaciones</a></li>
        </ul>
    </div>

    <div class="main-content" id="main-content">
        <div id="profile" class="content-section">
            <h2>Perfil</h2>
            <p>Email: {{ user.profile.email }}</p>
            <p>Edad: {{ user.profile.age }}</p>
        </div>
        <div id="settings" class="content-section">
            <h2>Configuración</h2>
            <p>La información de tu configuración va aquí.</p>
        </div>
        <div id="messages" class="content-section">
            <h2>Mensajes</h2>
            <p>Tus mensajes van aquí.</p>
        </div>
        <div id="notifications" class="content-section">
            <h2>Notificaciones</h2>
            <p>Tus notificaciones van aquí.</p>
        </div>
    </div>

    <script>
        document.getElementById('toggle-btn').addEventListener('click', function() {
            var sidebar = document.getElementById('sidebar');
            var mainContent = document.getElementById('main-content');
            sidebar.classList.toggle('active');
            mainContent.classList.toggle('active');
        });
    </script>
</body>
</html>

<?php //require_once '../app/Components/UserTableComponent.php'; ?>
<?php //echo \App\Components\UserTableComponent::generateTable(); ?>
<h1><?php //echo $message; ?></h1>

<!-- views/home.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="/assets/css/home.css">
</head>
<body>
    <nav>
        <div class="container">
            <div class="logo">Your Logo</div>
            <div class="navbar-links">
                <?php if ($loggedIn) : ?>
                    <span>{{ lang.welcome }}, <?php echo htmlspecialchars($user['name']); ?></span>
                    <a href="/user" class="btn">{{ lang.panel }}</a>
                    <a href="/logout" class="btn">{{ lang.logout }}</a>
                <?php else : ?>
                    <a href="/login" class="btn">{{ lang.login_nav }}</a>
                    <a href="/register" class="btn">{{ lang.register_nav }}</a>
                <?php endif; ?>
                <select id="language-selector" onchange="changeLanguage()">
                    <option value="en">English</option>
                    @if({{ locale }} == es)
                        <option value="es" selected="selected">Español</option>
                    @else
                        <option value="es">Español</option>
                    @endif
                </select>
            </div>
        </div>
    </nav>
    
    <div class="container">
        <h1>Bienvenido a la pagina HOME</h1>
        <p>rellena el contenido de la pagina.</p>
    </div>
</body>
<script>
    function changeLanguage() {
        var language = document.getElementById("language-selector").value;
        window.location.href = "/change-language?lang=" + language;
    }
</script>
</html>

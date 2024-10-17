<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>404 - Página No Encontrada</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f4f4;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        h1 {
            font-size: 10rem;
            color: #ff6b6b;
        }

        h2 {
            font-size: 2rem;
            margin-bottom: 20px;
        }

        p {
            font-size: 1.2rem;
            margin-bottom: 20px;
        }

        a {
            text-decoration: none;
            color: #ff6b6b;
            font-weight: bold;
            border: 2px solid #ff6b6b;
            padding: 10px 20px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        a:hover {
            background-color: #ff6b6b;
            color: #fff;
        }

        /* Responsivo */
        @media (max-width: 768px) {
            h1 {
                font-size: 6rem;
            }

            h2 {
                font-size: 1.8rem;
            }

            p {
                font-size: 1rem;
            }
        }

        @media (max-width: 480px) {
            h1 {
                font-size: 4rem;
            }

            h2 {
                font-size: 1.5rem;
            }

            p {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>404</h1>
        <h2>¡Ups! Página no encontrada</h2>
        <p>La página que estás buscando no existe o ha sido movida.</p>
        <a href="/">Volver a la página de inicio</a>
    </div>

</body>
</html>

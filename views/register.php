<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .errors {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .errors ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .errors li {
            margin: 5px 0;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
        }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            background: #28a745;
            color: white;
            padding: 10px 0;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Register</h1>
            @if( {{ is_array($errors) }} == true)
                <ul class="errors">
                    {{ @foreach ($errors as $field => $message) }}
                            <li>{{ $message }}</li>
                    {{ @endforeach }}
                </ul>
            @endif
        
        <form method="POST" action="/register">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="{{ name }}">
            <br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="{{ email }}">
            <br>

            <label for="age">Age:</label>
            <input type="text" id="age" name="age" value="{{ age }}">
            <br>

            <label for="salary">Salary:</label>
            <input type="text" id="salary" name="salary" value="{{ salary }}">
            <br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password">
            <br>

            <button type="submit">Register</button>
        </form>
    </div>
</body>
</html>

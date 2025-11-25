<?php


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wheel Fire Club - Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            max-width: 400px;
            width: 100%;
        }
        
        h1 {
            color: #667eea;
            text-align: center;
            margin-bottom: 30px;
            font-size: 28px;
        }
        
        h2 {
            color: #333;
            margin: 20px 0 15px;
            font-size: 18px;
        }
        
        form {
            margin-bottom: 25px;
        }
        
        input[type="text"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            margin-bottom: 10px;
            transition: border-color 0.3s;
        }
        
        input[type="text"]:focus {
            outline: none;
            border-color: #667eea;
        }
        
        button {
            width: 100%;
            padding: 12px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s;
        }
        
        button:hover {
            background: #5568d3;
        }
        
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .divider {
            height: 1px;
            background: #ddd;
            margin: 30px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Wheel Fire Club</h1>
        
        @if(session('error'))
            <div class="error">{{ session('error') }}</div>
        @endif

        <h2>Iniciar Sesión</h2>
        <form action="{{ route('player.login') }}" method="POST">
            @csrf
            <input type="text" name="name" placeholder="Nombre" required autofocus>
            <button type="submit">Entrar</button>
        </form>

        <div class="divider"></div>

        <h2>Registrarse</h2>
        <form action="{{ route('player.register') }}" method="POST">
            @csrf
            <input type="text" name="name" placeholder="Nombre" required>
            <button type="submit">Crear jugador</button>
        </form>
    </div>
</body>
</html>
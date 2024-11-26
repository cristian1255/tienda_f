<?php
session_start();

// Conexión a la base de datos
$host = "localhost";
$user = "root"; // Cambiar si es necesario
$pass = "12345"; // Cambiar si es necesario
$dbname = "tienda-f";

$conn = new mysqli($host, $user, $pass, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Procesar el formulario de inicio de sesión
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_usuario = trim($_POST['nombre_usuario']);
    $contraseña = trim($_POST['contraseña']);
    $perfil = trim($_POST['perfil']);

    // Verificar si el usuario existe en la base de datos
    $sql = "SELECT * FROM usuario WHERE nombre_usuario = ? AND perfil = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $nombre_usuario, $perfil);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        // Verificar la contraseña usando password_verify
        if (password_verify($contraseña, $row['contraseña'])) {
            // Iniciar sesión y guardar datos en la sesión
            $_SESSION['nombre_usuario'] = $nombre_usuario;
            $_SESSION['perfil'] = $perfil;

            // Redirigir según el perfil
            switch ($perfil) {
                case 'root':
                    header("Location: root.php");
                    break;
                case 'secretaria':
                    header("Location: secretaria.php");
                    break;
                case 'gerente':
                    header("Location: gerente.php");
                    break;
                case 'empleado':
                    header("Location: empleado.php");
                    break;
                default:
                    $error = "Perfil no reconocido.";
            }
            exit();
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "Usuario o perfil no encontrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Tienda F</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #5a5a5a; /* Fondo claro */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh; /* Altura completa de la ventana */
            margin: 0;
        }

        .login-container {
            background-color: #fff; /* Fondo blanco */
            border-radius: 10px; /* Bordes redondeados */
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); /* Sombra suave */
            padding: 40px; /* Espaciado interno */
            width: 300px; /* Ancho del contenedor */
            text-align: center;
        }
        input, select, button {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        h2 {
            margin-bottom: 20px; /* Espacio inferior del título */
            color: #333; /* Color del texto */
        }

        label {
            display: block; /* Hace que cada etiqueta ocupe una línea */
            margin-bottom: 5px; /* Espacio inferior de las etiquetas */
            text-align: left; /* Alinea el texto a la izquierda */
            color: #555; /* Color de texto más claro */
        }

        input[type="text"],
        input[type="password"],
        select {
            width: 100%; /* Ancho completo */
            padding: 10px; /* Espaciado interno */
            margin-bottom: 20px; /* Espacio inferior entre campos */
            border: 1px solid #ccc; /* Borde gris claro */
            border-radius: 5px; /* Bordes redondeados */
            box-sizing: border-box; /* Incluye el padding y el border en el ancho total */
        }

        input[type="submit"] {
            background-color: #2600ff; /* Color de fondo del botón */
            color: white; /* Color del texto */
            border: none; /* Sin borde */
            padding: 10px; /* Espaciado interno */
            border-radius: 5px; /* Bordes redondeados */
            cursor: pointer; /* Cambia el cursor al pasar sobre el botón */
            transition: background-color 0.3s; /* Transición suave para el color de fondo */
        }

        input[type="submit"]:hover {
            background-color: #000; /* Color del botón al pasar el mouse */
        }

        /* Estilo para el mensaje de error */
        .error {
            color: red; /* Color rojo para los errores */
            margin-top: 10px; /* Espacio superior */
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Iniciar Sesión</h1>
        <?php if (isset($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="nombre_usuario">Usuario:</label>
                <input type="text" id="nombre_usuario" name="nombre_usuario" required>
            </div>
            <div class="form-group">
                <label for="contraseña">Contraseña:</label>
                <input type="password" id="contraseña" name="contraseña" required>
            </div>
            <div class="form-group">
                <label for="perfil">Perfil:</label>
                <select id="perfil" name="perfil" required>
                    <option value="">>>>>Selecciona tu Perfil<<<<</option>
                    <option value="root">Root</option>
                    <option value="secretaria">Secretaria</option>
                    <option value="gerente">Gerente</option>
                    <option value="empleado">Empleado</option>
                </select>
            </div>
            <button type="submit">Iniciar Sesión</button>
        </form>
    </div>
</body>
</html>

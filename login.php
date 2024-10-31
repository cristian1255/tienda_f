<?php
// Iniciar la sesión
session_start();

// Conexión a la base de datos
$host = "localhost";
$user = "root"; // Cambiar si es necesario
$pass = "12345";     // Cambiar si es necesario
$dbname = "tienda-f";

$conn = new mysqli($host, $user, $pass, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Procesar el formulario de inicio de sesión
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['nombre_usuario'];
    $password = $_POST['contraseña'];
    $perfil = $_POST['perfil'];

    // Consulta para verificar las credenciales del usuario
    $sql = "SELECT * FROM usuario WHERE nombre_usuario = ? AND contraseña = ? AND perfil = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $usuario, $password, $perfil);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Usuario autenticado correctamente
        $_SESSION['nombre_usuario'] = $usuario;
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
                echo "Perfil no reconocido.";
        }
        exit();
    } else {
        // Credenciales incorrectas
        echo "Usuario o contraseña incorrectos.";
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
        <h2>Inicio de Sesión</h2>
        <form method="POST" action="">
            <label for="nombre_usuario">Usuario:</label>
            <input type="text" id="nombre_usuario" name="nombre_usuario" required>

            <label for="contraseña">Contraseña:</label>
            <input type="password" id="contraseña" name="contraseña" required>

            <label for="perfil">Perfil:</label>
            <select id="perfil" name="perfil" required>
            <option value="selecciona">Seleciona perfil</option>
                <option value="root">Root</option>
                <option value="secretaria">Secretaria</option>
                <option value="gerente">Gerente</option>
                <option value="empleado">Empleado</option>
            </select>

            <input type="submit" value="Iniciar Sesión">
        </form>
    </div>
</body>
</html>

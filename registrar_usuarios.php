<?php
session_start();
// Lista de perfiles con permisos de acceso
$perfiles_autorizados = ['root', 'secretaria', 'gerente', 'empleado']; // Puedes modificar esta lista para incluir los perfiles autorizados

// Verificar si el usuario tiene un perfil autorizado
if (!in_array($_SESSION['perfil'], $perfiles_autorizados)) {
    // Si el perfil no está autorizado, redirigir a login.php
    header("Location: login.php");
    exit();
}
include 'permisos.php'; // Incluir el archivo de permisos

$perfil = $_SESSION['perfil'];

// Verificar si el usuario tiene permiso para ver la tabla de usuarios (solo root)
if (!verificarPermisos($perfil, 'usuario', 'ver')) {
    die("No tienes permiso para ver esta página.");
}

// Conexión a la base de datos
$host = "localhost";
$user = "root"; // Cambiar si es necesario
$pass = "root";     // Cambiar si es necesario
$dbname = "tienda-f";

$conn = new mysqli($host, $user, $pass, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Procesar la inserción de un nuevo usuario
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['agregar'])) {
    if (!verificarPermisos($perfil, 'usuario', 'editar')) {
        echo "<div style='text-align: center;'>
                <p>No tienes permiso para agregar usuarios.</p>
                <form method='POST' action='registrar_usuarios.php'>
                    <button type='submit' class='btn'>Volver</button>
                </form>
              </div>";
        exit();
    }

    $nombre_usuario = $_POST['nombre_usuario'];
    $contraseña = $_POST['contraseña']; // No se encripta la contraseña
    $perfil_usuario = $_POST['perfil']; // Variable corregida para el perfil
    $created_at = date('Y-m-d H:i:s'); // Fecha actual

    // Usar la variable $perfil_usuario en bind_param
    $sql = "INSERT INTO usuario (nombre_usuario, contraseña, perfil, created_at) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nombre_usuario, $contraseña, $perfil_usuario, $created_at);

    if ($stmt->execute()) {
        echo "Nuevo usuario agregado exitosamente.";
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Procesar la edición de un usuario existente
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['guardar'])) {
    if (!verificarPermisos($perfil, 'usuario', 'editar')) {
        die("No tienes permiso para editar usuarios.");
    }

    $usuario_id = $_POST['usuario_id'];
    $nombre_usuario = $_POST['nombre_usuario'];
    $perfil_usuario = $_POST['perfil']; // Variable corregida para el perfil

    $sql = "UPDATE usuario SET nombre_usuario = ?, perfil = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $nombre_usuario, $perfil_usuario, $usuario_id);

    if ($stmt->execute()) {
        echo "Usuario actualizado exitosamente.";
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Obtener registros de la tabla usuario
$sql = "SELECT * FROM usuario";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Usuarios</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f7f7f7;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .btn {
            padding: 10px;
            color: white;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Registrar Usuarios</h1>

        <form method="POST" action="">
            <label for="nombre_usuario">Nombre de Usuario:</label>
            <input type="text" id="nombre_usuario" name="nombre_usuario" required>

            <label for="contraseña">Contraseña:</label>
            <input type="password" id="contraseña" name="contraseña" required>

            <label for="perfil">Perfil:</label>
            <select id="perfil" name="perfil" required>
                <option value="root">Root</option>
                <option value="secretaria">Secretaria</option>
                <option value="gerente">Gerente</option>
                <option value="empleado">Empleado</option>
            </select>

            <input type="submit" name="agregar" value="Agregar Usuario" class="btn">
        </form>

        <!-- Botón para volver al menú -->
        <div class="button-group">
            <form method="POST" action="<?php 
                // Determinar la acción según el perfil
                if ($_SESSION['perfil'] === 'root') {
                    echo 'root.php'; // Redirige al menú de root
                } elseif ($_SESSION['perfil'] === 'secretaria') {
                    echo 'secretaria.php'; // Redirige al menú de secretaria
                } elseif ($_SESSION['perfil'] === 'gerente') {
                    echo 'gerente.php'; // Redirige al menú de gerente
                } elseif ($_SESSION['perfil'] === 'empleado') {
                    echo 'empleado.php'; // Redirige al menú de empleado
                }
            ?>">
                <button type="submit" class="btn">Volver al menú</button>
            </form>
            
        </div>

        <table>
            <tr>
                <th>ID</th>
                <th>Nombre de Usuario</th>
                <th>Perfil</th>
                <th>Fecha de Creación</th>
                <?php if (verificarPermisos($perfil, 'usuario', 'editar')): ?>
                    <th>Acción</th> <!-- Columna para editar -->
                <?php endif; ?>
            </tr>
            <?php if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td>
                            <?php if (isset($_POST['editar']) && $_POST['usuario_id'] == $row['id']): ?>
                                <form method="POST" action="">
                                    <input type="hidden" name="usuario_id" value="<?php echo $row['id']; ?>">
                                    <input type="text" name="nombre_usuario" value="<?php echo $row['nombre_usuario']; ?>" required>
                                    <select name="perfil" required>
                                        <option value="root" <?php if ($row['perfil'] == 'root') echo 'selected'; ?>>Root</option>
                                        <option value="secretaria" <?php if ($row['perfil'] == 'secretaria') echo 'selected'; ?>>Secretaria</option>
                                        <option value="gerente" <?php if ($row['perfil'] == 'gerente') echo 'selected'; ?>>Gerente</option>
                                        <option value="empleado" <?php if ($row['perfil'] == 'empleado') echo 'selected'; ?>>Empleado</option>
                                    </select>
                                    <input type="submit" name="guardar" value="Guardar" class="btn">  <!-- Cambiado a 'guardar' -->
                                </form>
                            <?php else: ?>
                                <?php echo $row['nombre_usuario']; ?>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $row['perfil']; ?></td>
                        <td><?php echo $row['created_at']; ?></td>
                        <?php if (verificarPermisos($perfil, 'usuario', 'editar')): ?>
                            <td>
                                <form method="POST" action="">
                                    <input type="hidden" name="usuario_id" value="<?php echo $row['id']; ?>">
                                    <input type="submit" name="editar" value="Editar" class="btn">
                                </form>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php } 
            } else {
                echo "<tr><td colspan='5'>No hay usuarios registrados.</td></tr>";
            } ?>
        </table>
    </div>
</body>
</html>

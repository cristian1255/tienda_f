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

// Verificar si el usuario tiene permiso para ver la tabla de clientes
if (!verificarPermisos($perfil, 'clientes', 'ver')) {
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

// Procesar la inserción de un nuevo cliente
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['agregar'])) {
    if (!verificarPermisos($perfil, 'clientes', 'editar')) {
        echo "<div style='text-align: center;'>
                <p>No tienes permiso para agregar clientes.</p>
                <form method='POST' action='clientes.php'>
                    <button type='submit' class='btn'>Volver</button>
                </form>
              </div>";
        exit();
    }

    $nombre_cliente = $_POST['nombre_cliente'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];

    $sql = "INSERT INTO clientes (nombre_cliente, telefono, email) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $nombre_cliente, $telefono, $email);

    if ($stmt->execute()) {
        echo "Nuevo cliente agregado exitosamente.";
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Procesar la edición de un cliente existente
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['guardar'])) {
    if (!verificarPermisos($perfil, 'clientes', 'editar')) {
        die("No tienes permiso para editar clientes.");
    }

    $cliente_id = $_POST['cliente_id'];
    $nombre_cliente = $_POST['nombre_cliente'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];

    $sql = "UPDATE clientes SET nombre_cliente = ?, telefono = ?, email = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $nombre_cliente, $telefono, $email, $cliente_id);

    if ($stmt->execute()) {
        echo "Cliente actualizado exitosamente.";
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Procesar la eliminación de un cliente
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['eliminar'])) {
    if (!verificarPermisos($perfil, 'clientes', 'editar')) {
        die("No tienes permiso para eliminar clientes.");
    }

    $cliente_id = $_POST['cliente_id'];

    $sql = "DELETE FROM clientes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $cliente_id);

    if ($stmt->execute()) {
        echo "Cliente eliminado exitosamente.";
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Obtener registros de la tabla clientes
$sql = "SELECT * FROM clientes";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Clientes</title>
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
        .btn-danger {
            background-color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gestión de Clientes</h1>

        <form method="POST" action="">
            <label for="nombre_cliente">Nombre del Cliente:</label>
            <input type="text" id="nombre_cliente" name="nombre_cliente" required>

            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <input type="submit" name="agregar" value="Agregar Cliente" class="btn">
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
                <th>Nombre del Cliente</th>
                <th>Teléfono</th>
                <th>Email</th>
                <?php if (verificarPermisos($perfil, 'clientes', 'editar')): ?>
                    <th>Acción</th> <!-- Columna para editar y eliminar -->
                <?php endif; ?>
            </tr>
            <?php if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td>
                            <?php if (isset($_POST['editar']) && $_POST['cliente_id'] == $row['id']): ?>
                                <form method="POST" action="">
                                    <input type="hidden" name="cliente_id" value="<?php echo $row['id']; ?>">
                                    <input type="text" name="nombre_cliente" value="<?php echo $row['nombre_cliente']; ?>" required>
                                    <input type="text" name="telefono" value="<?php echo $row['telefono']; ?>" required>
                                    <input type="email" name="email" value="<?php echo $row['email']; ?>" required>
                                    <input type="submit" name="guardar" value="Guardar" class="btn">  <!-- Cambiado a 'guardar' -->
                                </form>
                            <?php else: ?>
                                <?php echo $row['nombre_cliente']; ?>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $row['telefono']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <?php if (verificarPermisos($perfil, 'clientes', 'editar')): ?>
                            <td>
                                <form method="POST" action="">
                                    <input type="hidden" name="cliente_id" value="<?php echo $row['id']; ?>">
                                    <input type="submit" name="editar" value="Editar" class="btn">
                                </form>
                                <form method="POST" action="" style="margin-top: 5px;">
                                    <input type="hidden" name="cliente_id" value="<?php echo $row['id']; ?>">
                                    <input type="submit" name="eliminar" value="Eliminar" class="btn btn-danger">
                                </form>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php } 
            } else {
                echo "<tr><td colspan='5'>No hay clientes registrados.</td></tr>";
            } ?>
        </table>
    </div>
</body>
</html>

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

// Verificar si el usuario tiene permiso para ver la tabla de proveedores
if (!verificarPermisos($perfil, 'proveedores', 'ver')) {
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

// Procesar la inserción de un nuevo proveedor
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['agregar'])) {
    if (!verificarPermisos($perfil, 'proveedores', 'editar')) {
        echo "<div style='text-align: center;'>
                <p>No tienes permiso para agregar proveedores.</p>
                <form method='POST' action='categorias.php'>
                    <button type='submit' class='btn'>Volver.</button>
                </form>
              </div>";
        exit();
    }

    $nombre_proveedor = $_POST['nombre_proveedor'];
    $contacto = $_POST['contacto'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];
    $created_at = date('Y-m-d H:i:s'); // Fecha actual

    $sql = "INSERT INTO proveedores (nombre_proveedor, contacto, telefono, direccion, created_at) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $nombre_proveedor, $contacto, $telefono, $direccion, $created_at);

    if ($stmt->execute()) {
        echo "Nuevo proveedor agregado exitosamente.";
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Procesar la edición de un proveedor existente
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['guardar'])) {
    if (!verificarPermisos($perfil, 'proveedores', 'editar')) {
        die("No tienes permiso para editar proveedores.");
    }

    $proveedor_id = $_POST['proveedor_id'];
    $nombre_proveedor = $_POST['nombre_proveedor'];
    $contacto = $_POST['contacto'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];

    $sql = "UPDATE proveedores SET nombre_proveedor = ?, contacto = ?, telefono = ?, direccion = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $nombre_proveedor, $contacto, $telefono, $direccion, $proveedor_id);

    if ($stmt->execute()) {
        echo "Proveedor actualizado exitosamente.";
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Obtener registros de la tabla proveedores
$sql = "SELECT * FROM proveedores";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Proveedores</title>
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
        <h1>Registrar Proveedores</h1>

        <form method="POST" action="">
            <label for="nombre_proveedor">Nombre del Proveedor:</label>
            <input type="text" id="nombre_proveedor" name="nombre_proveedor" required>

            <label for="contacto">Nombre del Contacto:</label>
            <input type="text" id="contacto" name="contacto" required>

            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono" required>

            <label for="direccion">Dirección:</label>
            <textarea id="direccion" name="direccion" required></textarea>

            <input type="submit" name="agregar" value="Agregar Proveedor" class="btn">
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
                <th>Nombre del Proveedor</th>
                <th>Contacto</th>
                <th>Teléfono</th>
                <th>Dirección</th>
                <th>Fecha de Creación</th>
                <?php if (verificarPermisos($perfil, 'proveedores', 'editar')): ?>
                    <th>Acción</th> <!-- Columna para editar -->
                <?php endif; ?>
            </tr>
            <?php if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td>
                            <?php if (isset($_POST['editar']) && $_POST['proveedor_id'] == $row['id']): ?>
                                <form method="POST" action="">
                                    <input type="hidden" name="proveedor_id" value="<?php echo $row['id']; ?>">
                                    <input type="text" name="nombre_proveedor" value="<?php echo $row['nombre_proveedor']; ?>" required>
                                    <input type="text" name="contacto" value="<?php echo $row['contacto']; ?>" required>
                                    <input type="text" name="telefono" value="<?php echo $row['telefono']; ?>" required>
                                    <textarea name="direccion" required><?php echo $row['direccion']; ?></textarea>
                                    <input type="submit" name="guardar" value="Guardar" class="btn">  <!-- Cambiado a 'guardar' -->
                                </form>
                            <?php else: ?>
                                <?php echo $row['nombre_proveedor']; ?>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $row['contacto']; ?></td>
                        <td><?php echo $row['telefono']; ?></td>
                        <td><?php echo $row['direccion']; ?></td>
                        <td><?php echo $row['created_at']; ?></td>
                        <?php if (verificarPermisos($perfil, 'proveedores', 'editar')): ?>
                            <td>
                                <form method="POST" action="">
                                    <input type="hidden" name="proveedor_id" value="<?php echo $row['id']; ?>">
                                    <input type="submit" name="editar" value="Editar" class="btn">
                                </form>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php } 
            } else {
                echo "<tr><td colspan='7'>No hay proveedores registrados.</td></tr>";
            } ?>
        </table>
    </div>
</body>
</html>

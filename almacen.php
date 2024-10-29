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

// Verificar si el usuario tiene permiso para ver la tabla de almacen
if (!verificarPermisos($perfil, 'almacen', 'ver')) {
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

// Procesar la inserción de un nuevo registro
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['agregar'])) {
    if (!verificarPermisos($perfil, 'almacen', 'editar')) {
        // Mostrar el mensaje y botón para volver al almacén
        echo "<div style='text-align: center;'>
                <p>No tienes permiso para agregar registros al almacén.</p>
                <form method='POST' action='almacen.php'>
                    <button type='submit' class='btn'>Volver al Almacén</button>
                </form>
              </div>";
        exit();
    }

    $producto_id = $_POST['producto_id'];
    $cantidad = $_POST['cantidad'];
    $fecha_actualizacion = date('Y-m-d H:i:s'); // Fecha actual

    $sql = "INSERT INTO almacen (producto_id, cantidad, fecha_actualizacion) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $producto_id, $cantidad, $fecha_actualizacion);

    if ($stmt->execute()) {
        echo "Nuevo registro agregado exitosamente.";
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Procesar la edición de un registro existente
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['guardar'])) {
    if (!verificarPermisos($perfil, 'almacen', 'editar')) {
        die("No tienes permiso para editar registros del almacén.");
    }

    $almacen_id = $_POST['almacen_id'];
    $producto_id = $_POST['producto_id'];
    $cantidad = $_POST['cantidad'];

    $sql = "UPDATE almacen SET producto_id = ?, cantidad = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $producto_id, $cantidad, $almacen_id);

    if ($stmt->execute()) {
        echo "Registro actualizado exitosamente.";
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Obtener registros de la tabla almacen
$sql = "SELECT * FROM almacen";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Almacén</title>
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
        <h1>Gestión de Almacén</h1>

        <form method="POST" action="">
            <label for="producto_id">ID del Producto:</label>
            <input type="number" id="producto_id" name="producto_id" required>

            <label for="cantidad">Cantidad:</label>
            <input type="number" id="cantidad" name="cantidad" required>

            <input type="submit" name="agregar" value="Agregar al Almacén" class="btn">
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
                <th>ID del Producto</th>
                <th>Cantidad</th>
                <th>Fecha de Actualización</th>
                <?php if (verificarPermisos($perfil, 'almacen', 'editar')): ?>
                    <th>Acción</th> <!-- Columna para editar -->
                <?php endif; ?>
            </tr>
            <?php if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td>
                            <?php if (isset($_POST['editar']) && $_POST['almacen_id'] == $row['id']): ?>
                                <form method="POST" action="">
                                    <input type="hidden" name="almacen_id" value="<?php echo $row['id']; ?>">
                                    <input type="number" name="producto_id" value="<?php echo $row['producto_id']; ?>" required>
                                    <input type="number" name="cantidad" value="<?php echo $row['cantidad']; ?>" required>
                                    <input type="submit" name="guardar" value="Guardar" class="btn">
                                </form>
                            <?php else: ?>
                                <?php echo $row['producto_id']; ?>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $row['cantidad']; ?></td>
                        <td><?php echo $row['fecha_actualizacion']; ?></td>
                        <?php if (verificarPermisos($perfil, 'almacen', 'editar')): ?>
                            <td>
                                <form method="POST" action="">
                                    <input type="hidden" name="almacen_id" value="<?php echo $row['id']; ?>">
                                    <input type="submit" name="editar" value="Editar" class="btn">
                                </form>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php } 
            } else {
                echo "<tr><td colspan='5'>No hay registros en el almacén.</td></tr>";
            } ?>
        </table>
    </div>
</body>
</html>

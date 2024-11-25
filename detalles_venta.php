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

// Verificar si el usuario tiene permiso para ver la tabla de detalles de venta
if (!verificarPermisos($perfil, 'detalles_venta', 'ver')) {
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

// Procesar la inserción de un nuevo detalle de venta
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['agregar'])) {
    if (!verificarPermisos($perfil, 'detalles_venta', 'editar')) {
        echo "<div style='text-align: center;'>
                <p>No tienes permiso para agregar detalles de venta.</p>
                <form method='POST' action='detalles_venta.php'>
                    <button type='submit' class='btn'>Volver</button>
                </form>
              </div>";
        exit();
    }

    $venta_id = $_POST['venta_id'];
    $producto_id = $_POST['producto_id'];
    $cantidad = $_POST['cantidad'];
    $precio_unitario = $_POST['precio_unitario'];

    $sql = "INSERT INTO detalles_venta (venta_id, producto_id, cantidad, precio_unitario) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiid", $venta_id, $producto_id, $cantidad, $precio_unitario);

    if ($stmt->execute()) {
        echo "Nuevo detalle de venta agregado exitosamente.";
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Procesar la edición de un detalle de venta existente
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['guardar'])) {
    if (!verificarPermisos($perfil, 'detalles_venta', 'editar')) {
        die("No tienes permiso para editar detalles de venta.");
    }

    $detalle_id = $_POST['detalle_id'];
    $venta_id = $_POST['venta_id'];
    $producto_id = $_POST['producto_id'];
    $cantidad = $_POST['cantidad'];
    $precio_unitario = $_POST['precio_unitario'];

    $sql = "UPDATE detalles_venta SET venta_id = ?, producto_id = ?, cantidad = ?, precio_unitario = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiidi", $venta_id, $producto_id, $cantidad, $precio_unitario, $detalle_id);

    if ($stmt->execute()) {
        echo "Detalle de venta actualizado exitosamente.";
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Procesar la eliminación de un detalle de venta
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['eliminar'])) {
    if (!verificarPermisos($perfil, 'detalles_venta', 'editar')) {
        die("No tienes permiso para eliminar detalles de venta.");
    }

    $detalle_id = $_POST['detalle_id'];

    $sql = "DELETE FROM detalles_venta WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $detalle_id);

    if ($stmt->execute()) {
        echo "Detalle de venta eliminado exitosamente.";
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Obtener registros de la tabla detalles_venta
$sql = "SELECT detalles_venta.id, detalles_venta.venta_id, detalles_venta.producto_id, detalles_venta.cantidad, detalles_venta.precio_unitario 
        FROM detalles_venta";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Detalles de Venta</title>
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
        <h1>Gestión de Detalles de Venta</h1>

        <form method="POST" action="">
            <label for="venta_id">ID de la Venta:</label>
            <input type="number" id="venta_id" name="venta_id" required>

            <label for="producto_id">ID del Producto:</label>
            <input type="number" id="producto_id" name="producto_id" required>

            <label for="cantidad">Cantidad:</label>
            <input type="number" id="cantidad" name="cantidad" required>

            <label for="precio_unitario">Precio Unitario:</label>
            <input type="number" step="0.01" id="precio_unitario" name="precio_unitario" required>

            <input type="submit" name="agregar" value="Agregar Detalle de Venta" class="btn">
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
                <th>ID de la Venta</th>
                <th>ID del Producto</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <?php if (verificarPermisos($perfil, 'detalles_venta', 'editar')): ?>
                    <th>Acción</th> <!-- Columna para editar y eliminar -->
                <?php endif; ?>
            </tr>
            <?php if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td>
                            <?php if (isset($_POST['editar']) && $_POST['detalle_id'] == $row['id']): ?>
                                <form method="POST" action="">
                                    <input type="hidden" name="detalle_id" value="<?php echo $row['id']; ?>">
                                    <input type="number" name="venta_id" value="<?php echo $row['venta_id']; ?>" required>
                                    <input type="number" name="producto_id" value="<?php echo $row['producto_id']; ?>" required>
                                    <input type="number" name="cantidad" value="<?php echo $row['cantidad']; ?>" required>
                                    <input type="number" step="0.01" name="precio_unitario" value="<?php echo $row['precio_unitario']; ?>" required>
                                    <input type="submit" name="guardar" value="Guardar" class="btn">
                                </form>
                            <?php else: ?>
                                <?php echo $row['venta_id']; ?>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $row['producto_id']; ?></td>
                        <td><?php echo $row['cantidad']; ?></td>
                        <td><?php echo $row['precio_unitario']; ?></td>
                        <?php if (verificarPermisos($perfil, 'detalles_venta', 'editar')): ?>
                            <td>
                                <form method="POST" action="">
                                    <input type="hidden" name="detalle_id" value="<?php echo $row['id']; ?>">
                                    <input type="submit" name="editar" value="Editar" class="btn">
                                </form>
                                <form method="POST" action="">
                                    <input type="hidden" name="detalle_id" value="<?php echo $row['id']; ?>">
                                    <input type="submit" name="eliminar" value="Eliminar" class="btn btn-danger">
                                </form>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php } 
            } else {
                echo "<tr><td colspan='6'>No hay detalles de venta registrados.</td></tr>";
            } ?>
        </table>
    </div>
</body>
</html>

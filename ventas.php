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

// Verificar si el usuario tiene permiso para ver la tabla de ventas
if (!verificarPermisos($perfil, 'ventas', 'ver')) {
    die("No tienes permiso para ver esta página.");
}

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

// Procesar la inserción de una nueva venta
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['agregar'])) {
    // Verificar si el usuario tiene permiso para agregar/editar ventas
    if (!verificarPermisos($perfil, 'ventas', 'editar')) {
        die("No tienes permiso para agregar o editar ventas.");
    }

    $fecha_venta = date('Y-m-d');  // Fecha actual
    $total = $_POST['total'];
    $cliente_id = $_POST['cliente_id'];
    $usuario_id = $_SESSION['id'];  // Asumimos que el ID del usuario está guardado en la sesión

    $sql = "INSERT INTO ventas (fecha_venta, total, cliente_id, usuario_id) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdii", $fecha_venta, $total, $cliente_id, $usuario_id);

    if ($stmt->execute()) {
        echo "Nueva venta agregada exitosamente.";
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Procesar la edición de una venta existente
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['guardar'])) {
    if (!verificarPermisos($perfil, 'ventas', 'editar')) {
        die("No tienes permiso para editar ventas.");
    }

    $venta_id = $_POST['venta_id'];
    $total = $_POST['total'];
    $cliente_id = $_POST['cliente_id'];

    $sql = "UPDATE ventas SET total = ?, cliente_id = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("dii", $total, $cliente_id, $venta_id);

    if ($stmt->execute()) {
        echo "Venta actualizada exitosamente.";
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Procesar la eliminación de una venta
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['eliminar'])) {
    if (!verificarPermisos($perfil, 'ventas', 'editar')) {
        die("No tienes permiso para eliminar ventas.");
    }

    $venta_id = $_POST['venta_id'];
    $sql = "DELETE FROM ventas WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $venta_id);

    if ($stmt->execute()) {
        echo "Venta eliminada exitosamente.";
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Obtener registros de la tabla ventas
$sql = "SELECT ventas.id, ventas.fecha_venta, ventas.total, clientes.nombre_cliente, usuario.nombre_usuario 
        FROM ventas 
        INNER JOIN clientes ON ventas.cliente_id = clientes.id 
        LEFT JOIN usuario AS usuario ON ventas.usuario_id = usuario.id"; // Cambiado a LEFT JOIN
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Ventas</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f7f7f7; }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; background-color: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); }
        h1 { text-align: center; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; }
        .btn { padding: 10px; color: white; background-color: #007bff; border: none; border-radius: 5px; cursor: pointer; }
        .btn:hover { background-color: #0056b3; }
        .btn-delete { background-color: #dc3545; }
        .btn-delete:hover { background-color: #c82333; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gestión de Ventas</h1>

        <form method="POST" action="">
            <label for="total">Total de la Venta:</label>
            <input type="number" step="0.01" id="total" name="total" required>

            <label for="cliente_id">ID del Cliente:</label>
            <input type="number" id="cliente_id" name="cliente_id" required>

            <input type="submit" name="agregar" value="Agregar Venta" class="btn">
        </form>

        <!-- Botón para volver al menú -->
        <div class="button-group">
            <form method="POST" action="<?php 
                // Determinar la acción según el perfil
                if ($_SESSION['perfil'] === 'root') { echo 'root.php'; } 
                elseif ($_SESSION['perfil'] === 'secretaria') { echo 'secretaria.php'; } 
                elseif ($_SESSION['perfil'] === 'gerente') { echo 'gerente.php'; } 
                elseif ($_SESSION['perfil'] === 'empleado') { echo 'empleado.php'; }
            ?>">
                <button type="submit" class="btn">Volver al menú</button>
            </form>
        </div>

        <table>
            <tr>
                <th>ID</th>
                <th>Fecha de la Venta</th>
                <th>Total</th>
                <th>Nombre del Cliente</th>
                <th>Usuario</th>
                <?php if (verificarPermisos($perfil, 'ventas', 'editar')): ?>
                    <th>Editar</th>
                    <th>Eliminar</th>
                <?php endif; ?>
            </tr>
            <?php if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['fecha_venta']; ?></td>
                        <td>
                            <?php if (isset($_POST['editar']) && $_POST['venta_id'] == $row['id']): ?>
                                <form method="POST" action="">
                                    <input type="hidden" name="venta_id" value="<?php echo $row['id']; ?>">
                                    <input type="number" step="0.01" name="total" value="<?php echo $row['total']; ?>" required>
                                    <input type="number" name="cliente_id" value="<?php echo $row['cliente_id']; ?>" required>
                                    <input type="submit" name="guardar" value="Guardar" class="btn">
                                </form>
                            <?php else: ?>
                                <?php echo $row['total']; ?>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $row['nombre_cliente']; ?></td>
                        <td><?php echo isset($row['nombre_usuario']) ? $row['nombre_usuario'] : 'Usuario no asignado'; ?></td> <!-- Manejo de NULL -->
                        <?php if (verificarPermisos($perfil, 'ventas', 'editar')): ?>
                            <td>
                                <form method="POST" action="">
                                    <input type="hidden" name="venta_id" value="<?php echo $row['id']; ?>">
                                    <input type="submit" name="editar" value="Editar" class="btn">
                                </form>
                            </td>
                            <td>
                                <form method="POST" action="">
                                    <input type="hidden" name="venta_id" value="<?php echo $row['id']; ?>">
                                    <input type="submit" name="eliminar" value="Eliminar" class="btn btn-delete">
                                </form>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php } 
            } else {
                echo "<tr><td colspan='7'>No hay ventas registradas.</td></tr>";
            } ?>
        </table>
    </div>
</body>
</html>

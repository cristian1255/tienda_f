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

// Verificar si el usuario tiene permiso para ver la tabla de productos
if (!verificarPermisos($perfil, 'productos', 'ver')) {
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

// Procesar la inserción de un nuevo producto
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['agregar'])) {
    if (!verificarPermisos($perfil, 'productos', 'editar')) {
        echo "<div style='text-align: center;'>
                <p>No tienes permiso para agregar productos.</p>
                <form method='POST' action='registrar_productos.php'>
                    <button type='submit' class='btn'>Volver</button>
                </form>
              </div>";
        exit();
    }

    $nombre_producto = $_POST['nombre_producto'];
    $categoria_id = $_POST['categoria_id'];
    $descripcion = $_POST['descripcion'];
    $proveedor_id = $_POST['proveedor_id'];

    $sql = "INSERT INTO productos (nombre_producto, categoria_id, descripcion, proveedor_id) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisi", $nombre_producto, $categoria_id, $descripcion, $proveedor_id);

    if ($stmt->execute()) {
        echo "Nuevo producto agregado exitosamente.";
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Procesar la edición de un producto existente
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['guardar'])) {
    if (!verificarPermisos($perfil, 'productos', 'editar')) {
        die("No tienes permiso para editar productos.");
    }

    $producto_id = $_POST['producto_id'];
    $nombre_producto = $_POST['nombre_producto'];
    $categoria_id = $_POST['categoria_id'];
    $descripcion = $_POST['descripcion'];
    $proveedor_id = $_POST['proveedor_id'];

    $sql = "UPDATE productos SET nombre_producto = ?, categoria_id = ?, descripcion = ?, proveedor_id = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisii", $nombre_producto, $categoria_id, $descripcion, $proveedor_id, $producto_id);

    if ($stmt->execute()) {
        echo "Producto actualizado exitosamente.";
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Procesar la eliminación de un producto
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['eliminar'])) {
    if (!verificarPermisos($perfil, 'productos', 'editar')) {
        die("No tienes permiso para eliminar productos.");
    }

    $producto_id = $_POST['producto_id'];
    $sql = "DELETE FROM productos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $producto_id);

    if ($stmt->execute()) {
        echo "Producto eliminado exitosamente.";
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Obtener registros de la tabla productos
$sql = "SELECT productos.id, productos.nombre_producto, categorias.nombre_categoria, proveedores.nombre_proveedor, productos.descripcion 
        FROM productos 
        INNER JOIN categorias ON productos.categoria_id = categorias.id 
        INNER JOIN proveedores ON productos.proveedor_id = proveedores.id";
$result = $conn->query($sql);

// Obtener lista de categorías
$sql_categorias = "SELECT * FROM categorias";
$result_categorias = $conn->query($sql_categorias);

// Obtener lista de proveedores
$sql_proveedores = "SELECT * FROM proveedores";
$result_proveedores = $conn->query($sql_proveedores);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Productos</title>
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
        input, select, button {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
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
        .btn-delete {
            background-color: #dc3545;
        }
        .btn-delete:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Registrar Productos</h1>

        <form method="POST" action="">
            <label for="nombre_producto">Nombre del Producto:</label>
            <input type="text" id="nombre_producto" name="nombre_producto" required>

            <label for="categoria_id">ID de la Categoría:</label>
            <select id="categoria_id" name="categoria_id" required>
                <?php while ($row_categoria = $result_categorias->fetch_assoc()) { ?>
                    <option value="<?php echo $row_categoria['id']; ?>"><?php echo $row_categoria['nombre_categoria']; ?></option>
                <?php } ?>
            </select>

            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" required></textarea>

            <label for="proveedor_id">ID del Proveedor:</label>
            <select id="proveedor_id" name="proveedor_id" required>
                <?php while ($row_proveedor = $result_proveedores->fetch_assoc()) { ?>
                    <option value="<?php echo $row_proveedor['id']; ?>"><?php echo $row_proveedor['nombre_proveedor']; ?></option>
                <?php } ?>
            </select>

            <input type="submit" name="agregar" value="Agregar Producto" class="btn">
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
                <th>Nombre del Producto</th>
                <th>Categoría</th>
                <th>Proveedor</th>
                <th>Descripción</th>
                <?php if (verificarPermisos($perfil, 'productos', 'editar')): ?>
                    <th>Acciones</th> <!-- Columna para editar y eliminar -->
                <?php endif; ?>
            </tr>
            <?php if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td>
                            <?php if (isset($_POST['editar']) && $_POST['producto_id'] == $row['id']): ?>
                                <form method="POST" action="">
                                    <input type="hidden" name="producto_id" value="<?php echo $row['id']; ?>">
                                    <input type="text" name="nombre_producto" value="<?php echo $row['nombre_producto']; ?>" required>
                                    <select name="categoria_id" required>
                                        <?php
                                        // Reposicionar las categorías para el selector
                                        $result_categorias->data_seek(0); // Reiniciar el puntero del resultado
                                        while ($row_categoria = $result_categorias->fetch_assoc()) { ?>
                                            <option value="<?php echo $row_categoria['id']; ?>" <?php if ($row_categoria['id'] == $row['categoria_id']) echo 'selected'; ?>>
                                                <?php echo $row_categoria['nombre_categoria']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    <input type="text" name="descripcion" value="<?php echo $row['descripcion']; ?>" required>
                                    <select name="proveedor_id" required>
                                        <?php
                                        // Reposicionar los proveedores para el selector
                                        $result_proveedores->data_seek(0); // Reiniciar el puntero del resultado
                                        while ($row_proveedor = $result_proveedores->fetch_assoc()) { ?>
                                            <option value="<?php echo $row_proveedor['id']; ?>" <?php if ($row_proveedor['id'] == $row['proveedor_id']) echo 'selected'; ?>>
                                                <?php echo $row_proveedor['nombre_proveedor']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    <input type="submit" name="guardar" value="Guardar" class="btn">
                                </form>
                            <?php else: ?>
                                <?php echo $row['nombre_producto']; ?>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $row['nombre_categoria']; ?></td>
                        <td><?php echo $row['nombre_proveedor']; ?></td>
                        <td><?php echo $row['descripcion']; ?></td>
                        <?php if (verificarPermisos($perfil, 'productos', 'editar')): ?>
                            <td>
                                <form method="POST" action="">
                                    <input type="hidden" name="producto_id" value="<?php echo $row['id']; ?>">
                                    <input type="submit" name="editar" value="Editar" class="btn">
                                    <input type="submit" name="eliminar" value="Eliminar" class="btn btn-delete" onclick="return confirm('¿Estás seguro de que deseas eliminar este producto?');">
                                </form>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php } 
            } else {
                echo "<tr><td colspan='7'>No hay productos registrados.</td></tr>";
            } ?>
        </table>
    </div>
</body>
</html>

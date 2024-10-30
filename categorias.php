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

// Verificar si el usuario tiene permiso para ver la tabla de categorías
if (!verificarPermisos($perfil, 'categorias', 'ver')) {
    die("No tienes permiso para ver esta página.");
}

// Conexión a la base de datos
$host = "localhost";
$user = "root"; // Cambiar si es necesario
$pass = "root"; // Cambiar si es necesario
$dbname = "tienda-f";

$conn = new mysqli($host, $user, $pass, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Procesar la inserción de una nueva categoría
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['agregar'])) {
    if (!verificarPermisos($perfil, 'categorias', 'editar')) {
        // Mostrar el mensaje y botón para volver al almacén
        echo "<div style='text-align: center;'>
                <p>No tienes permiso para agregar categorías.</p>
                <form method='POST' action='categorias.php'>
                    <button type='submit' class='btn'>Volver a Categorías</button>
                </form>
              </div>";
        exit();
    }

    $nombre_categoria = $_POST['nombre_categoria'];

    $sql = "INSERT INTO categorias (nombre_categoria) VALUES (?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $nombre_categoria);

    if ($stmt->execute()) {
        echo "Nueva categoría agregada exitosamente.";
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Procesar la edición de una categoría existente
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['guardar'])) {
    if (!verificarPermisos($perfil, 'categorias', 'editar')) {
        die("No tienes permiso para editar categorías.");
    }

    $categoria_id = $_POST['categoria_id'];
    $nombre_categoria = $_POST['nombre_categoria'];

    $sql = "UPDATE categorias SET nombre_categoria = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $nombre_categoria, $categoria_id);

    if ($stmt->execute()) {
        echo "Categoría actualizada exitosamente.";
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Procesar la eliminación de una categoría
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['eliminar'])) {
    if (!verificarPermisos($perfil, 'categorias', 'editar')) {
        die("No tienes permiso para eliminar categorías.");
    }

    $categoria_id = $_POST['categoria_id'];

    $sql = "DELETE FROM categorias WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $categoria_id);

    if ($stmt->execute()) {
        echo "Categoría eliminada exitosamente.";
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Obtener registros de la tabla categorías
$sql = "SELECT * FROM categorias";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Categorías</title>
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
        <h1>Gestión de Categorías</h1>

        <form method="POST" action="">
            <label for="nombre_categoria">Nombre de la Categoría:</label>
            <input type="text" id="nombre_categoria" name="nombre_categoria" required>
            <input type="submit" name="agregar" value="Agregar Categoría" class="btn">
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
                <th>Nombre de la Categoría</th>
                <?php if (verificarPermisos($perfil, 'categorias', 'editar')): ?>
                    <th>Acción</th> <!-- Columna para editar y eliminar -->
                <?php endif; ?>
            </tr>
            <?php if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td>
                            <?php if (isset($_POST['editar']) && $_POST['categoria_id'] == $row['id']): ?>
                                <form method="POST" action="">
                                    <input type="hidden" name="categoria_id" value="<?php echo $row['id']; ?>">
                                    <input type="text" name="nombre_categoria" value="<?php echo $row['nombre_categoria']; ?>" required>
                                    <input type="submit" name="guardar" value="Guardar" class="btn">
                                </form>
                            <?php else: ?>
                                <?php echo $row['nombre_categoria']; ?>
                            <?php endif; ?>
                        </td>
                        <?php if (verificarPermisos($perfil, 'categorias', 'editar')): ?>
                            <td>
                                <form method="POST" action="">
                                    <input type="hidden" name="categoria_id" value="<?php echo $row['id']; ?>">
                                    <input type="submit" name="editar" value="Editar" class="btn">
                                </form>
                                <form method="POST" action="" style="margin-top: 5px;">
                                    <input type="hidden" name="categoria_id" value="<?php echo $row['id']; ?>">
                                    <input type="submit" name="eliminar" value="Eliminar" class="btn btn-danger">
                                </form>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php } 
            } else {
                echo "<tr><td colspan='3'>No hay categorías registradas.</td></tr>";
            } ?>
        </table>
    </div>
</body>
</html>

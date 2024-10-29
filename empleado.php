<?php
session_start();
if ($_SESSION['perfil'] !== 'empleado') {
    header("Location: login.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
    <center><h1>Bienvenido, Empleado(a)</h1></center>
    <center><h2>Puedes visualizar las siguientes tablas</h2></center>
        <!-- Botones de funcionalidades -->
        <div class="button-group">
            <a href="categorias.php" class="btn">Categorias</a>
            <a href="ventas.php" class="btn">Ventas</a>
            <a href="clientes.php" class="btn">Clientes</a>
        </div>
        <div class="button-group">
            <a href="detalles_venta.php" class="btn">Detalles_ventas</a>
            <a href="registrar_productos.php" class="btn">Productos</a>
        </div>
        <!-- Botón para cerrar sesión -->
        <div class="button-group">
            <form method="POST" action="cerrar_sesion.php">
                <button type="submit" class="btn cerrar-sesion">Cerrar Sesión</button>
            </form>
        </div> 
    </div>
</body>
</html>

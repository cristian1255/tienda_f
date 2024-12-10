<?php
// permisos.php

// Función para verificar los permisos de los usuarios según su perfil
function verificarPermisos($perfil, $tabla, $accion) {
    // Permisos para cada perfil
    $permisos = [
        'root' => ['ver' => '*', 'editar' => '*'],  // Root puede ver y editar todo
        'gerente' => [
            'ver' => '*', 
            'editar' => ['clientes', 'productos', 'ventas', 'proveedores', 'almacen', 'detalles_venta', 'categorias', 'usuario']
        ],  // Gerente puede ver todo y editar todo
        'secretaria' => [
            'ver' => ['clientes', 'productos', 'ventas', 'detalles_venta', 'categorias'],
            'editar' => ['clientes', 'productos', 'ventas', 'detalles_venta', 'categorias']
        ],  // Secretaria puede ver y editar todo menos la tabla usuario
        'empleado' => [
            'ver' => ['clientes', 'productos', 'ventas', 'detalles_venta', 'categorias'], 
            'editar' => []
        ]  // Empleado puede ver algunas tablas y solo editar ventas y clientes
    ];

     // Verificar si el perfil tiene permisos completos ('*')
     if ($permisos[$perfil][$accion] === '*') {
        return true;  // Tiene acceso completo
    }

    // Verificar si el perfil tiene permisos específicos
    return in_array($tabla, $permisos[$perfil][$accion]);
}

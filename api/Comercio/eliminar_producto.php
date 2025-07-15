<?php
session_start();

if (!isset($_SESSION['id_comercio'])) {
    echo json_encode(["status" => "error", "message" => "No tienes permiso para eliminar productos."]);
    exit;
}

$id_comercio = $_SESSION['id_comercio'];

require_once("../../catalogo-conexion/Conexion.php");

if ($conexion->connect_error) {
    echo json_encode(["status" => "error", "message" => "Error de conexión: " . $conexion->connect_error]);
    exit;
}

if (isset($_GET['id'])) {
    $id_producto = intval($_GET['id']);

    // Verificar que el producto sea del comercio actual
    $check_sql = "SELECT id_producto, imagen FROM productos WHERE id_producto = ? AND id_comercio = ?";
    $check_stmt = $conexion->prepare($check_sql);
    $check_stmt->bind_param("ii", $id_producto, $id_comercio);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        $producto = $result->fetch_assoc();
        $imagen = $producto['imagen'];

        // Eliminar de la base
        $delete_sql = "DELETE FROM productos WHERE id_producto = ?";
        $delete_stmt = $conexion->prepare($delete_sql);
        $delete_stmt->bind_param("i", $id_producto);
        if ($delete_stmt->execute()) {
            if (file_exists("../uploads/" . $imagen)) {
                unlink("../uploads/" . $imagen); // eliminar imagen física
            }
            echo json_encode(["status" => "success", "message" => "Producto eliminado correctamente."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error al eliminar el producto."]);
        }
        $delete_stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Producto no encontrado o no pertenece al comercio."]);
    }

    $check_stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "ID de producto no proporcionado."]);
}
 
$conexion->close();

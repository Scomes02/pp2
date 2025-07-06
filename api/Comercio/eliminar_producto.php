<?php
session_start();

if (!isset($_SESSION['id_comercio'])) {
    echo json_encode(["status" => "error", "message" => "No tienes permiso para eliminar productos."]);
    exit;
}

$id_comercio = $_SESSION['id_comercio'];

require_once("../catalogo-conexion/Conexion.php");

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Error de conexión: " . $conn->connect_error]);
    exit;
}

if (isset($_GET['id'])) {
    $id_producto = intval($_GET['id']);

    // Verificar si el producto pertenece al comercio
    $check_sql = "SELECT id_producto, imagen FROM productos WHERE id_producto = ? AND id_comercio = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $id_producto, $id_comercio);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        // Obtener el nombre de la imagen antes de eliminar el producto
        $product = $check_result->fetch_assoc();
        $imagen = $product['imagen'];

        // Eliminar el producto de la base de datos
        $delete_sql = "DELETE FROM productos WHERE id_producto = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $id_producto);
        if ($delete_stmt->execute()) {
            // Eliminar la imagen físicamente
            $imagen_path = "../uploads/" . $imagen;
            if (file_exists($imagen_path)) {
                unlink($imagen_path); // Elimina el archivo de la imagen
            }

            echo json_encode(["status" => "success", "message" => "Producto eliminado correctamente."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error al eliminar el producto."]);
        }
        $delete_stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "No tienes permiso para eliminar este producto."]);
    }
    $check_stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "ID de producto no proporcionado."]);
}

?>

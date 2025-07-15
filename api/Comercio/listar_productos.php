<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

if (!isset($_SESSION['id_comercio'])) {
    echo json_encode(["status" => "error", "message" => "No tienes permiso para ver productos."]);
    exit;
}

$id_comercio = $_SESSION['id_comercio'];

require_once("../../catalogo-conexion/Conexion.php");

if ($conexion->connect_error) {
    echo json_encode(["status" => "error", "message" => "Error de conexión: " . $conexion->connect_error]);
    exit;
}

$sql = "SELECT id_producto, nombre, codigo_producto, precio_unitario, descuento, imagen 
        FROM productos 
        WHERE id_comercio = ?";
$stmt = $conexion->prepare($sql);
if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "Error en la consulta: " . $conexion->error]);
    exit;
}

$stmt->bind_param("i", $id_comercio);
$stmt->execute();
$result = $stmt->get_result();

$productos = [];
while ($row = $result->fetch_assoc()) {
    // Convertir a valores adecuados
    $row['precio_unitario'] = floatval($row['precio_unitario']);
    $row['descuento'] = $row['descuento'] !== null ? floatval($row['descuento']) : null;

    $productos[] = $row;
}

if (count($productos) > 0) {
    echo json_encode(["status" => "success", "products" => $productos], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(["status" => "error", "message" => "No se encontraron productos."]);
}

$stmt->close();
$conexion->close();
?>

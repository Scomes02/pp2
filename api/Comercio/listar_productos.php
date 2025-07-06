<?php
header('Content-Type: application/json');
session_start();

// Habilitar el reporte de errores
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['id_comercio'])) {
    echo json_encode(["status" => "error", "message" => "No tienes permiso para ver productos."]);
    exit;
}

$id_comercio = $_SESSION['id_comercio'];

require_once("../catalogo-conexion/Conexion.php");

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Error de conexión: " . $conn->connect_error]);
    exit;
}

// Consulta para obtener productos
$sql = "SELECT id_producto, nombre_producto, codigo_producto, precio, off, imagen FROM productos WHERE id_comercio = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    echo json_encode(["status" => "error", "message" => "Error en la preparación de la consulta SQL: " . $conn->error]);
    exit;
}

$stmt->bind_param("i", $id_comercio);
$stmt->execute();
$result = $stmt->get_result();

// Comprobar si hay resultados
if ($result->num_rows > 0) {
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    echo json_encode(["status" => "success", "products" => $products]);
} else {
    echo json_encode(["status" => "error", "message" => "No se encontraron productos."]);
}

$stmt->close();
$conn->close();
?>
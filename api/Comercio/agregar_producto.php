<?php
session_start();

require_once("../../catalogo-conexion/Conexion.php");
 
if (!isset($_SESSION['id_comercio'])) {
    echo json_encode(["status" => "error", "message" => "No se ha iniciado sesión como comercio."]);
    exit;
}

$id_comercio = $_SESSION['id_comercio'];

// Validar conexión
if ($conexion->connect_error) {
    echo json_encode(["status" => "error", "message" => "Error de conexión: " . $conexion->connect_error]);
    exit;
}

// Verificar que el método sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "message" => "Método no permitido."]);
    exit;
}

// Capturar campos del formulario
$nombre = trim($_POST['nombre'] ?? '');
$codigo = trim($_POST['codigo_producto'] ?? '');
$precio = floatval($_POST['precio'] ?? 0);
$descuento = isset($_POST['off']) ? floatval($_POST['off']) : null;

// Validar campos obligatorios
if ($nombre === '' || $codigo === '' || $precio <= 0) {
    echo json_encode(["status" => "error", "message" => "Campos obligatorios incompletos."]);
    exit;
}

// Manejo de imagen
$uploadDir = "../uploads/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(["status" => "error", "message" => "Error al subir la imagen."]);
    exit;
}

$imagen_nombre = uniqid("img_") . "_" . basename($_FILES['imagen']['name']);
$imagen_ruta = $uploadDir . $imagen_nombre;

if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $imagen_ruta)) {
    echo json_encode(["status" => "error", "message" => "No se pudo guardar la imagen en el servidor."]);
    exit;
}

// Insertar en la base de datos
$sql = "INSERT INTO productos (nombre, codigo_producto, precio_unitario, descuento, imagen, id_comercio)
        VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conexion->prepare($sql);
if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "Error al preparar SQL: " . $conexion->error]);
    exit;
}

$stmt->bind_param("ssdssi", $nombre, $codigo, $precio, $descuento, $imagen_nombre, $id_comercio);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Producto agregado exitosamente."]);
} else {
    echo json_encode(["status" => "error", "message" => "Error al insertar: " . $stmt->error]);
}

$stmt->close();
$conexion->close();

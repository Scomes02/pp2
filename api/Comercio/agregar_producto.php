<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['id_comercio'])) {
    echo json_encode(["status" => "error", "message" => "No se ha iniciado sesión como comercio."]);
    exit;
}
$id_comercio = $_SESSION['id_comercio'];

// Conexión a la base de datos
require_once("../catalogo-conexion/Conexion.php");
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Error de conexión: " . $conn->connect_error]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $codigo_producto = $_POST['codigo_producto'];
    $precio = $_POST['precio'];
    $off = isset($_POST['off']) && $_POST['off'] !== '' ? $_POST['off'] : null;

    // Manejo de la imagen
    $uploadDir = "../uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $imagen_nombre = uniqid() . "_" . basename($_FILES['imagen']['name']);
        $imagen_ruta = $uploadDir . $imagen_nombre;

        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $imagen_ruta)) {
            $sql = "INSERT INTO productos (nombre_producto, codigo_producto, precio, off, imagen, id_comercio) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            
            if ($stmt === false) {
                echo json_encode(["status" => "error", "message" => "Error en la preparación de la consulta SQL: " . $conn->error]);
                exit;
            }

            $stmt->bind_param("ssdsdi", $nombre, $codigo_producto, $precio, $off, $imagen_nombre, $id_comercio);

            if ($stmt->execute()) {
                echo json_encode(["status" => "success", "message" => "Producto agregado correctamente."]);
            } else {
                echo json_encode(["status" => "error", "message" => "Error al agregar el producto: " . $stmt->error]);
            }
            $stmt->close();
        } else {
            echo json_encode(["status" => "error", "message" => "Error al mover la imagen al directorio de destino."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Error al subir la imagen."]);
    }
}

$conn->close();
?>

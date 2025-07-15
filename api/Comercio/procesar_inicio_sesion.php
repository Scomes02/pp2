<?php
session_start();
require_once("../../catalogo-conexion/Conexion.php");

// Verificar conexión
if ($conexion->connect_error) {
    die(json_encode(["status" => "error", "message" => "Error de conexión a la base de datos: " . $conexion->connect_error]));
}

// Verificar que se reciba método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Obtener y normalizar tipo de usuario
    $tipo_usuario = strtolower(trim($_POST['tipo_usuario'] ?? ''));

    // Validar tipo de usuario
    if (!in_array($tipo_usuario, ['cliente', 'comercio'])) {
        echo json_encode(["status" => "error", "message" => "Tipo de usuario inválido."]);
        exit;
    }

    // Obtener nombre según tipo de usuario
    $nombre = $tipo_usuario === 'cliente' 
                ? trim($_POST['nombre_cliente'] ?? '') 
                : trim($_POST['nombre_comercio'] ?? '');

    $clave = $_POST['Clave'] ?? '';

    // Validar campos
    if (empty($nombre) || empty($clave)) {
        echo json_encode(["status" => "error", "message" => "Debe completar todos los campos."]);
        exit;
    }

    // Buscar usuario
    $sql = "SELECT id_usuario, password_hash FROM usuarios WHERE nombre = ? AND tipo_usuario = ?";
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "Error preparando la consulta: " . $conexion->error]);
        exit;
    }

    $stmt->bind_param("ss", $nombre, $tipo_usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 0) {
        echo json_encode(["status" => "error", "message" => "Usuario no encontrado."]);
        exit;
    }

    $usuario = $resultado->fetch_assoc();

    // Verificar contraseña
    if (!password_verify($clave, $usuario['password_hash'])) {
        echo json_encode(["status" => "error", "message" => "Contraseña incorrecta."]);
        exit;
    }

    $id_usuario = $usuario['id_usuario'];

    // Redireccionar según el tipo de usuario
    if ($tipo_usuario === 'comercio') {
        $sql = "SELECT id_comercio FROM comercios WHERE id_usuario = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows === 0) {
            echo json_encode(["status" => "error", "message" => "Datos de comercio no encontrados."]);
            exit;
        }

        $comercio = $res->fetch_assoc();
        $_SESSION['id_comercio'] = $comercio['id_comercio'];
        header("Location: ../../vistas/Comercio/RocketApp-COM.php");
        exit;
    }

    if ($tipo_usuario === 'cliente') {
        $sql = "SELECT id_cliente FROM clientes WHERE id_usuario = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows === 0) {
            echo json_encode(["status" => "error", "message" => "Datos de cliente no encontrados."]);
            exit;
        }

        $cliente = $res->fetch_assoc();
        $_SESSION['id_cliente'] = $cliente['id_cliente'];
        header("Location: ../../vistas/Cliente/RocketApp-CL.php");
        exit;
    }
}

// Cerrar conexión
$conexion->close();
?>
 
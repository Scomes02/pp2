<?php
session_start();

// Conexión a la base de datos
require_once("../catalogo-conexion/Conexion.php");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_comercio = $_POST['nombre_comercio'];
    $clave = $_POST['Clave'];
    $tipo_usuario = $_POST['tipo_usuario'];

    // Validar que los datos no estén vacíos
    if (empty($nombre_comercio) || empty($clave) || empty($tipo_usuario)) {
        echo json_encode(["status" => "error", "message" => "Debe completar todos los campos."]);
        exit;
    }

    // Verificar el tipo de usuario
    if ($tipo_usuario === "Comercio") {
        // Buscar el comercio en la base de datos
        $sql = "SELECT id_comercio, clave FROM comercios WHERE nombre_comercio = ?";
        $stmt = $conn->prepare($sql);

        // Comprobar si la preparación de la consulta fue exitosa
        if ($stmt === false) {
            die("Error en la preparación de la consulta: " . $conn->error);
        }

        $stmt->bind_param("s", $nombre_comercio);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $comercio = $result->fetch_assoc();
            if (password_verify($clave, $comercio['clave'])) {
                $_SESSION['id_comercio'] = $comercio['id_comercio'];
                header("Location: RocketApp-COM.php");
                exit;
            } else {
                echo json_encode(["status" => "error", "message" => "Contraseña incorrecta."]);
            }            
        } else {
            echo json_encode(["status" => "error", "message" => "Usuario no encontrado."]);
        }

        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Seleccione un tipo válido (Cliente o Comercio)."]);
    }
}

$conn->close();
?>
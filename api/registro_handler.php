<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Conexión a la base de datos
    require_once '../catalogo-conexion/Conexion.php';
    $conn = $conexion;

    if ($conn->connect_error) {
        die(json_encode(['status' => 'error', 'message' => 'Error de conexión a la base de datos.']));  // Mejora en la respuesta
    }

    // Capturar los datos del formulario
    $tipo = $_POST['tipo'] ?? null;
    $usuario = $_POST['Usuario'] ?? '';
    $telefono = $_POST['Telefono'] ?? '';
    $clave = $_POST['Clave'] ?? '';
    $nombre_completo = $_POST['Nombre_completo'] ?? '';
    $dni = $_POST['DNI'] ?? '';
    $correo = $_POST['Correo'] ?? '';
    $rclave = $_POST['RClave'] ?? '';

    // Validaciones básicas
    if (empty($tipo) || empty($usuario) || empty($telefono) || empty($clave) || empty($nombre_completo) || empty($dni) || empty($correo) || empty($rclave)) {
        echo json_encode(['status' => 'error', 'message' => 'Por favor, complete todos los campos.']);
        exit;
    }

    if ($clave !== $rclave) {
        echo json_encode(['status' => 'error', 'message' => 'Las claves no coinciden.']);
        exit;
    }

    // Validar que el correo y el DNI sean únicos (si es necesario)
    $sql_check_email = "SELECT * FROM clientes WHERE correo = ? UNION SELECT * FROM comercios WHERE correo = ?";
    $stmt_check_email = $conn->prepare($sql_check_email);
    $stmt_check_email->bind_param('ss', $correo, $correo);
    $stmt_check_email->execute();
    $result_check_email = $stmt_check_email->get_result();
    if ($result_check_email->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'El correo ya está registrado.']);
        exit;
    }

    $sql_check_dni = "SELECT * FROM clientes WHERE dni = ? UNION SELECT * FROM comercios WHERE dni = ?";
    $stmt_check_dni = $conn->prepare($sql_check_dni);
    $stmt_check_dni->bind_param('ss', $dni, $dni);
    $stmt_check_dni->execute();
    $result_check_dni = $stmt_check_dni->get_result();
    if ($result_check_dni->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'El DNI ya está registrado.']);
        exit;
    }

    // Encriptar la clave
    $clave_hash = password_hash($clave, PASSWORD_DEFAULT);

    // Determinar la tabla de destino según el tipo de usuario
    if ($tipo === 'Cliente') {
        $tabla = 'clientes';
    } elseif ($tipo === 'Comercio') {
        $tabla = 'comercios';
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Tipo de usuario no válido.']);
        exit;
    }

    // Insertar los datos en la tabla correspondiente
    $sql = "INSERT INTO $tabla (usuario, telefono, clave, nombre_completo, dni, correo) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssssss', $usuario, $telefono, $clave_hash, $nombre_completo, $dni, $correo);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Usuario registrado exitosamente.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al registrar el usuario: ' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}

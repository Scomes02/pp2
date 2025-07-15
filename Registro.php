<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'catalogo-conexion/Conexion.php';
    $conn = $conexion;

    if ($conn->connect_error) {
        die(json_encode(['status' => 'error', 'message' => 'Error de conexión a la base de datos.']));
    }

    // Capturar y sanitizar
    $tipo = $_POST['tipo'] ?? null;
    $nombre = trim($_POST['nombre_completo'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $dni_cuit = trim($_POST['dni_cuit'] ?? '');
    $email = filter_var(trim($_POST['correo'] ?? ''), FILTER_VALIDATE_EMAIL);
    $clave = $_POST['clave'] ?? '';
    $rclave = $_POST['rclave'] ?? '';

    // Validaciones
    if (!$tipo || !in_array($tipo, ['Cliente', 'Comercio'])) {
        echo json_encode(['status' => 'error', 'message' => 'Tipo de usuario inválido.']);
        exit;
    }
    if (!$nombre || !$telefono || !$direccion || !$dni_cuit || !$email || !$clave || !$rclave) {
        echo json_encode(['status' => 'error', 'message' => 'Todos los campos son obligatorios.']);
        exit;
    }
    if ($clave !== $rclave) {
        echo json_encode(['status' => 'error', 'message' => 'Las contraseñas no coinciden.']);
        exit;
    }

    // Verificar si ya existe email o dni_cuit
    $verifica_sql = "SELECT id_usuario FROM usuarios WHERE email = ? OR dni_cuit = ?";
    $verifica_stmt = $conn->prepare($verifica_sql);
    $verifica_stmt->bind_param("ss", $email, $dni_cuit);
    $verifica_stmt->execute();
    $verifica_stmt->store_result();
    if ($verifica_stmt->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'El correo o DNI/CUIT ya están registrados.']);
        exit;
    }
    $verifica_stmt->close();

    // Encriptar clave
    $password_hash = password_hash($clave, PASSWORD_DEFAULT);
    $tipo_usuario = strtolower($tipo); // cliente o comercio

    // Insertar en usuarios
    $insert_usuario = "INSERT INTO usuarios (nombre, telefono, direccion, email, dni_cuit, password_hash, tipo_usuario) 
                       VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_usuario);
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Error preparando consulta usuarios.']);
        exit;
    }
    $stmt->bind_param("sssssss", $nombre, $telefono, $direccion, $email, $dni_cuit, $password_hash, $tipo_usuario);
    if (!$stmt->execute()) {
        echo json_encode(['status' => 'error', 'message' => 'Error al registrar usuario: ' . $stmt->error]);
        exit;
    }
    $id_usuario = $stmt->insert_id;
    $stmt->close();

    // Insertar en cliente o comercio
    if ($tipo_usuario === 'cliente') {
        $sql_sub = "INSERT INTO clientes (id_usuario) VALUES (?)";
    } else {
        $sql_sub = "INSERT INTO comercios (id_usuario) VALUES (?)";
    } 

    $stmt_sub = $conn->prepare($sql_sub);
    $stmt_sub->bind_param("i", $id_usuario);
    if (!$stmt_sub->execute()) {
        echo json_encode(['status' => 'error', 'message' => 'Error al crear registro secundario: ' . $stmt_sub->error]);
        exit;
    }

    $stmt_sub->close();
    $conn->close();

    // Redirigir con éxito
    header("Location: Index.php?success=registro");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/img/poke-icono.ico">
    <title>Registro - RocketApp</title>
    <link rel="stylesheet" href="assets/css/style2.css">
    <script src="https://kit.fontawesome.com/8fa0212ec6.js" crossorigin="anonymous"></script>
</head>

<body>
    <main>
        <form id="registroForm" method="POST" action="registro.php">
            <h1>Registro</h1>
            <div class="checkbox-group">
                <label>
                    <input type="radio" name="tipo" value="Cliente" required> Cliente
                </label>
                <br>
                <label>
                    <input type="radio" name="tipo" value="Comercio" required> Comercio
                </label>
            </div>
            <div class="input-group">
                <br>
                <label>Nombre Completo</label>
                <input type="text" name="nombre_completo" required>

                <label>Teléfono</label>
                <input type="tel" name="telefono" required>

                <label>Dirección</label>
                <input type="text" name="direccion" required>

                <label>DNI/CUIT</label>
                <input type="text" name="dni_cuit" required>

                <label>Correo Electrónico</label>
                <input type="email" name="correo" required>

                <label>Contraseña</label>
                <input type="password" name="clave" required>

                <label>Repetir Contraseña</label>
                <input type="password" name="rclave" required>
            </div>
            <button type="submit">Registrarse</button>
            <a href="Index.php">Salir</a>
        </form>
    </main>
</body>

</html>
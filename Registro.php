<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Conexión a la base de datos
    require_once '../catalogo-conexion/Conexion.php';
    $conn = $conexion;

    if ($conn->connect_error) {
        die(json_encode(['status' => 'error', 'message' => 'Error de conexión a la base de datos.']));
    }

    // Sanitizar y capturar datos
    $tipo = $_POST['tipo'] ?? null;
    $nombre_completo = trim($_POST['nombre_completo'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $dni_cuit = trim($_POST['dni_cuit'] ?? '');
    $correo = filter_var(trim($_POST['correo'] ?? ''), FILTER_VALIDATE_EMAIL);
    $clave = $_POST['clave'] ?? '';
    $rclave = $_POST['rclave'] ?? '';

    // Validaciones
    if (!$tipo || ($tipo !== 'Cliente' && $tipo !== 'Comercio')) {
        echo json_encode(['status' => 'error', 'message' => 'Seleccione un tipo válido (Cliente o Comercio).']);
        exit;
    }
    if (!$nombre_completo || !$telefono || !$direccion || !$dni_cuit || !$correo || !$clave || !$rclave) {
        echo json_encode(['status' => 'error', 'message' => 'Todos los campos son obligatorios.']);
        exit;
    }
    if ($clave !== $rclave) {
        echo json_encode(['status' => 'error', 'message' => 'Las contraseñas no coinciden.']);
        exit;
    }

    // Encriptar la clave
    $clave_hash = password_hash($clave, PASSWORD_DEFAULT);

    // Determinar la tabla y los campos según el tipo
    $tabla = $tipo === 'Cliente' ? 'clientes' : 'comercios';
    $nombre_campo = $tipo === 'Cliente' ? 'nombre_cliente' : 'nombre_comercio';

    // Preparar la consulta
    $sql = "INSERT INTO $tabla ($nombre_campo, telefono, direccion, dni_cuit, correo, clave) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Error al preparar la consulta.']);
        exit;
    }

    $stmt->bind_param('ssssss', $nombre_completo, $telefono, $direccion, $dni_cuit, $correo, $clave_hash);

    // Ejecutar e informar del resultado
    if ($stmt->execute()) {
        header("Location: Index.php?success=registro");
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al registrar el usuario: ' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../assets/img/poke-icono.ico">
    <title>Registro - RocketApp</title>
    <link rel="stylesheet" href="../assets/css/style2.css">
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
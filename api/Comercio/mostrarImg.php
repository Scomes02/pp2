<?php
session_start();

if (!isset($_SESSION['id_comercio'])) {
    echo "Error: No se encontró el ID del comercio en la sesión.";
    exit;
}

$id_comercio = $_SESSION['id_comercio'];

require_once("../../catalogo-conexion/Conexion.php");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$sql = "SELECT id_producto, nombre, codigo_producto, precio_unitario, descuento, imagen 
        FROM productos 
        WHERE id_comercio = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_comercio);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Productos del Comercio</title>
    <link rel="stylesheet" href="../assets/css/style3Com.css"> <!-- si tenés CSS -->
</head>
<body>
    <h1>Productos del Comercio</h1>
    <div class="productos-container">
<?php
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='producto'>";
        echo "<img src='../../uploads/" . htmlspecialchars($row['imagen']) . "' alt='" . htmlspecialchars($row['nombre']) . "' style='max-width:150px;'>";
        echo "<h3>" . htmlspecialchars($row['nombre']) . "</h3>";
        echo "<p>Código: " . htmlspecialchars($row['codigo_producto']) . "</p>";
        echo "<p>Precio: $" . number_format($row['precio_unitario'], 2) . "</p>";
        if ($row['descuento'] !== null && $row['descuento'] > 0) {
            echo "<p>Descuento: " . htmlspecialchars($row['descuento']) . "%</p>";
        }
        echo "</div>";
    } 
} else {
    echo "<p>No hay productos cargados para este comercio.</p>";
}
?>
    </div>
</body>
</html>
<?php
$stmt->close();
$conexion->close();
?>

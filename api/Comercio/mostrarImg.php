<?php
session_start(); // Inicia la sesión

// Verifica si el comercio ha iniciado sesión
if (!isset($_SESSION['id_comercio'])) {
    echo "Error: No se encontró el ID del comercio en la sesión.";
    exit;
}

$id_comercio = $_SESSION['id_comercio']; // ID del comercio actual

// Conexión a la base de datos
require_once("../catalogo-conexion/Conexion.php");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$sql = "SELECT id_producto, nombre_producto, codigo_producto, precio, imagen FROM productos WHERE id_comercio = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_comercio);
$stmt->execute();
$result = $stmt->get_result();

// Muestra los productos
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='producto'>";
        echo "<img src='../uploads/" . htmlspecialchars($row['imagen']) . "' alt='" . htmlspecialchars($row['nombre_producto']) . "'>";
        echo "<h3>" . htmlspecialchars($row['nombre_producto']) . "</h3>";
        echo "<p>Código: " . htmlspecialchars($row['codigo_producto']) . "</p>";
        echo "<p>Precio: $" . number_format($row['precio'], 2) . "</p>";
        if (!empty($row['codigo_producto'])) {
            echo "<p>Codigo_producto: " . htmlspecialchars($row['codigo_producto']) . "</p>";
        }
        echo "</div>";
    }
} else {
    echo "<p>No hay productos cargados para este comercio.</p>";
}

$stmt->close();
$conn->close();

?>

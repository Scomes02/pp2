<?php
require_once("../../catalogo-conexion/Conexion.php");

// Verificar conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}
 
$sql = "SELECT * FROM productos";
$result = $conexion->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<p class="product-price">$' . $row['precio_unitario'] . '</p>';
        if ($row["descuento"]) {
            echo '<s>$' . ($row['precio_unitario'] * (1 - $row['descuento'] / 100)) . '</s>';
        }
    }
} else {
    echo "<p>0 productos encontrados</p>";
}

$conn->close();

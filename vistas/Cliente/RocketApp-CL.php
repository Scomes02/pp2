<?php
session_start();
if (!isset($_SESSION['id_cliente'])) {
    echo json_encode(["status" => "error", "message" => "No ha iniciado sesión como cliente."]);
    exit;
}
$tipo_usuario = $_SESSION['id_cliente'];

// Conexión a base de datos
require_once("../../catalogo-conexion/Conexion.php");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
} 
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../assets/img/poke-icono.ico">
    <title>Sistema Cliente - RocketApp</title>
    <link rel="stylesheet" href="../../assets/css/style3CL.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <header>
        <a href="../../Index.php"><i class="fa-solid fa-right-from-bracket"></i>Salir</a>
        <h1>Rocket App</h1>
    </header>

    <div class="search-bar">
        <input type="text" placeholder="Buscar">
        <button>🔍</button>
    </div>

    <div class="new-business">
        ¡Nuevos Negocios! Descubrí todo lo nuevo.
    </div>

    <section class="carousel-container">
        <div class="carru">
            <div id="carouselExample1" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="../../assets/img/chinito.jpg" class="d-block w-100" alt="Librería El Chinito">
                    </div>
                    <div class="carousel-item">
                        <img src="../../assets/img/miss.jpg" class="d-block w-100" alt="Perfumería Miss">
                    </div>
                    <div class="carousel-item">
                        <img src="../../assets/img/boulbasour-unscreen.gif" class="d-block w-100" alt="Bulbasaur">
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample1" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                    <span class="visually-hidden">Anterior</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExample1" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                    <span class="visually-hidden">Siguiente</span>
                </button>
            </div>
        </div>
    </section>

    <div class="product-list">
        <?php
        $sql = "SELECT p.*, u.nombre AS nombre_comercio
                FROM productos p
                JOIN comercios c ON p.id_comercio = c.id_comercio
                JOIN usuarios u ON c.id_usuario = u.id_usuario
                ORDER BY p.id_producto DESC";
        $result = $conexion->query($sql);

        if ($result->num_rows > 0) {
            while ($producto = $result->fetch_assoc()) {
                echo '<div class="product">';
                echo '<img src="../../uploads/' . htmlspecialchars($producto['imagen']) . '" alt="' . htmlspecialchars($producto['nombre_producto']) . '">';
                echo '<div class="product-info">';
                echo '<p>' . htmlspecialchars($producto['nombre_producto']) . '</p>';
                echo '<span class="product-price">$' . number_format($producto['precio'], 2) . '</span>';
                echo '<p class="text-muted">Descuento: ' . ($producto['off'] ? $producto['off'] . '%' : 'No') . '</p>';
                echo '</div>';
                echo '<div class="product-logo">';
                echo '<small>De: ' . htmlspecialchars($producto['nombre_comercio']) . '</small>';
                echo '</div>';
                echo '</div>';
            }
        } else {
            echo "<p>No hay productos disponibles.</p>";
        }
        ?>
    </div>

    <footer>
        <img src="../../assets/img/home-icon.png" alt="Home">
        <img src="../../assets/img/shop-icon.png" alt="Shop">
        <img src="../../assets/img/profile-icon.png" alt="Profile">
    </footer>
</body>

</html>

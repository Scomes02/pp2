<?php
session_start();

// Verificar si el usuario ha iniciado sesión como comercio
if (!isset($_SESSION['id_comercio'])) {
    echo json_encode(["status" => "error", "message" => "No se ha iniciado sesión como comercio."]);
    exit;
}
$id_comercio = $_SESSION['id_comercio'];

// Conexión a la base de datos
require_once("../../catalogo-conexion/Conexion.php");
if ($conexion->connect_error) {
    echo json_encode(["status" => "error", "message" => "Error de conexión: " . $conexion->connect_error]);
    exit;
}

// Obtener los datos del usuario asociado al comercio
$sql = "SELECT u.nombre 
        FROM comercios c 
        INNER JOIN usuarios u ON c.id_usuario = u.id_usuario 
        WHERE c.id_comercio = ?";
$stmt = $conexion->prepare($sql);
if ($stmt) {
    $stmt->bind_param("i", $id_comercio);
    $stmt->execute();
    $result = $stmt->get_result();

    $nombre_comercio = "Comercio Desconocido";
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $nombre_comercio = htmlspecialchars($row['nombre']);
    }

    $stmt->close();
} else {
    $nombre_comercio = "Error al obtener nombre";
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Rocket App</title>
    <link rel="stylesheet" href="../../assets/css/style3Com.css">
    <link rel="icon" href="../../assets/img/poke-icono.ico">
    <script src="https://kit.fontawesome.com/8fa0212ec6.js" crossorigin="anonymous"></script>
</head>

<body>
    <header>
        <a href="../../Index.php"><i class="fa-solid fa-right-from-bracket"></i>Salir</a>
        <h1>Rocket App</h1>
        <h2><?= $nombre_comercio ?></h2>
    </header>

    <!-- Agregar Producto -->
    <div class="product-input">
        <form id="add-product-form" method="POST" enctype="multipart/form-data" action="../../api/Comercio/agregar_producto.php">
            <input type="text" name="nombre" placeholder="Ingrese nombre del producto" required>
            <hr>
            <input type="text" name="codigo_producto" placeholder="Código del producto" required>
            <hr>
            <input type="number" name="precio" placeholder="Precio unitario" step="0.01" required>
            <hr>
            <input type="number" name="off" placeholder="OFF%" step="0.01">
            <hr>
            <input type="file" name="imagen" accept="image/*" required>
            <br>
            <button type="submit">Agregar producto</button>
        </form>
        <div id="response-message"></div>
    </div>

    <div class="product-list" id="product-list">
        <h2>Tus productos</h2>
        <div id="products-container"></div>
    </div>

    <footer>
        <img src="../../assets/img/home-icon.png" alt="Home">
        <img src="../../assets/img/shop-icon.png" alt="Shop">
        <img src="../../assets/img/profile-icon.png" alt="Profile">
    </footer>

    <script>
        document.getElementById("add-product-form").addEventListener("submit", function(e) {
            e.preventDefault(); // Evita que recargue la página

            const formData = new FormData(this);

            fetch("../../api/Comercio/agregar_producto.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    const messageDiv = document.getElementById("response-message");
                    messageDiv.textContent = data.message;
                    messageDiv.style.color = data.status === "success" ? "green" : "red";

                    if (data.status === "success") {
                        this.reset(); // Limpia el formulario
                        loadProducts(); // Recarga productos
                    }
                })
                .catch(error => {
                    console.error("Error en el envío:", error);
                    document.getElementById("response-message").textContent = "Error al enviar los datos.";
                });
        });

        function loadProducts() {
            fetch("../../api/Comercio/listar_productos.php")
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success") {
                        let container = document.getElementById("products-container");
                        container.innerHTML = "";
 
                        data.products.forEach(product => {
                            const div = document.createElement("div");
                            div.classList.add("product-block");
                            div.innerHTML = `
                                <h3>${product.nombre}</h3>
                                <p>Código: ${product.codigo_producto}</p>
                                <p>Precio: $${product.precio_unitario}</p>
                                <p>OFF: ${product.descuento || "No aplica"}</p>
                                <img src="../../uploads/${product.imagen}" alt="${product.nombre}">
                                <button onclick="editProduct(${product.id_producto})">Editar</button>
                                <button onclick="deleteProduct(${product.id_producto})">Eliminar</button>
                            `;
                            container.appendChild(div);
                        });
                    } else {
                        console.log("No se encontraron productos.");
                    }
                }).catch(error => {
                    console.error("Error al cargar productos:", error);
                });
        }

        window.onload = loadProducts;

        function deleteProduct(productId) {
            if (confirm("¿Estás seguro de que deseas eliminar este producto?")) {
                fetch(`eliminar_producto.php?id=${productId}`, {
                        method: "GET"
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === "success") loadProducts();
                        else alert("Error al eliminar.");
                    }).catch(err => console.error(err));
            }
        }
    </script>
</body>

</html>
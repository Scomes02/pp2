<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['id_comercio'])) {
    echo json_encode(["status" => "error", "message" => "No se ha iniciado sesión como comercio."]);
    exit;
}
$id_comercio = $_SESSION['id_comercio'];

// Conexión a la base de datos
require_once("../catalogo-conexion/Conexion.php");
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Error de conexión: " . $conn->connect_error]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
    $codigo_producto = filter_input(INPUT_POST, 'codigo_producto', FILTER_SANITIZE_STRING);
    $precio = filter_input(INPUT_POST, 'precio', FILTER_VALIDATE_FLOAT);
    $off = isset($_POST['off']) && $_POST['off'] !== '' ? filter_var($_POST['off'], FILTER_VALIDATE_FLOAT) : null;

    // Manejo de la imagen
    $uploadDir = "../uploads/";
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true)) {
        echo json_encode(["status" => "error", "message" => "No se pudo crear el directorio de subidas."]);
        exit;
    }

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $imagen_nombre = uniqid() . "_" . basename($_FILES['imagen']['name']);
        $imagen_ruta = $uploadDir . $imagen_nombre;

        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $imagen_ruta)) {
            $sql = "INSERT INTO productos (nombre_producto, descripcion, precio, id_comercio, imagen) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("ssdis", $nombre, $codigo_producto, $precio, $id_comercio, $imagen_nombre);

                if ($stmt->execute()) {
                    echo json_encode(["status" => "success", "message" => "Producto agregado correctamente."]);
                } else {
                    echo json_encode(["status" => "error", "message" => "Error al agregar el producto: " . $stmt->error]);
                }
                $stmt->close();
            } else {
                echo json_encode(["status" => "error", "message" => "Error en la consulta: " . $conn->error]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Error al mover la imagen al directorio de destino."]);
        }
    } else {
        switch ($_FILES['imagen']['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $error = "El archivo excede el tamaño permitido.";
                break;
            case UPLOAD_ERR_PARTIAL:
                $error = "El archivo se subió parcialmente.";
                break;
            case UPLOAD_ERR_NO_FILE:
                $error = "No se subió ningún archivo.";
                break;
            default:
                $error = "Error desconocido al subir el archivo.";
                break;
        }
        echo json_encode(["status" => "error", "message" => $error]);
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../assets/img/poke-icono.ico">
    <title>Rocket App</title>
    <link rel="stylesheet" href="../assets/css/style3Com.css">
    <link rel="icon" href="../assets/img/poke-icono.ico">
    <script src="https://kit.fontawesome.com/8fa0212ec6.js" crossorigin="anonymous"></script>
</head>

<body>
    <header>
        <a href="../Index.php"><i class="fa-solid fa-right-from-bracket"></i>Salir</a>
        <h1>Rocket App</h1>
        <?php
        $sql = "SELECT nombre_comercio FROM comercios WHERE id_comercio = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $id_comercio);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $nombre_comercio = htmlspecialchars($row['nombre_comercio']);
                echo "<h2>$nombre_comercio</h2>";
            } else {
                echo "<h2>Comercio Desconocido</h2>";
            }
            $stmt->close();
        } else {
            echo "<h2>Error al obtener el nombre del comercio</h2>";
        }
        ?>
    </header>
    <div class="product-input">
        <form id="add-product-form" method="POST" enctype="multipart/form-data">
            <label for="product-name"></label>
            <input type="text" id="product-name" name="nombre" placeholder="Ingrese nombre del producto" required>
            <hr>
            <label for="product-codigo"></label>
            <input type="text" id="product-codigo" name="codigo_producto" placeholder="Código del producto" required>
            <hr>
            <label for="product-price"></label>
            <input type="number" id="product-price" name="precio" placeholder="Precio unitario" step="0.01" required>
            <hr>
            <label for="product-off"></label>
            <input type="number" id="product-off" name="off" placeholder="OFF%" step="0.01">
            <hr>
            <label for="product-image"></label>
            <input type="file" id="product-image" name="imagen" accept="image/*" required>
            <br>
            <button type="submit" id="add-product-button">Agregar producto</button>
        </form>
        <div id="response-message"></div>
    </div>

    <div class="product-list" id="product-list">
        <h2>Tus productos</h2>
        <div id="products-container"></div>
    </div>

    <footer>
        <img src="../assets/img/home-icon.png" alt="Home">
        <img src="../assets/img/shop-icon.png" alt="Shop">
        <img src="../assets/img/profile-icon.png" alt="Profile">
    </footer>

    <script>
        function loadProducts() {
            fetch("listar_productos.php")
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success" && Array.isArray(data.products)) {
                        var productsContainer = document.getElementById("products-container");
                        productsContainer.innerHTML = "";

                        data.products.forEach((product) => {
                            var productDiv = document.createElement("div");
                            productDiv.classList.add("product-block");
                            productDiv.innerHTML = `
                                <h3>${product.nombre_producto}</h3>
                                <p>Código: ${product.codigo_producto}</p>
                                <p>Precio: $${product.precio}</p>
                                <p>OFF: ${product.off || "No aplica"}</p>
                                <img src="../uploads/${product.imagen}" alt="${product.nombre_producto}">
                                <button onclick="editProduct(${product.id_producto})">Editar</button>
                                <button onclick="deleteProduct(${product.id_producto})">Eliminar</button>
                            `;
                            productsContainer.appendChild(productDiv);
                        });
                    } else {
                        console.log("No se encontraron productos o la estructura de la respuesta no es válida.");
                    }
                })
                .catch((error) => {
                    console.error("Error al cargar productos:", error);
                });
        }

        window.onload = function() {
            loadProducts();
        };

        function editProduct(productId) {
            console.log("Editar producto con ID: " + productId);
        }

        function deleteProduct(productId) {
            if (confirm("¿Estás seguro de que deseas eliminar este producto?")) {
                fetch(`eliminar_producto.php?id=${productId}`, {
                        method: "GET",
                    })
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.status === "success") {
                            //alert("Producto eliminado correctamente.");
                            loadProducts();
                        } else {
                            alert("Error al eliminar el producto.");
                        }
                    })
                    .catch((error) => {
                        console.error("Error al eliminar el producto:", error);
                    });
            }
        }
    </script>
</body>

</html>

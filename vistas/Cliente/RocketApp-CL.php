<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../img/poke-icono.ico">
    <title>Sistema Cliente - Cliente</title>
    <link rel="stylesheet" href="../catalogo-productos/style3CL.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <header>
        <a href="../Index.php"><i class="fa-solid fa-right-from-bracket"></i>Salir</a>
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
            <form>
                <div id="carouselExample1" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="../assets/img/chinito.jpg" class="d-block w-100" alt="Librería El Chinito">
                        </div>
                        <div class="carousel-item">
                            <img src="../assets/img/miss.jpg" class="d-block w-100" alt="Perfumería Miss">
                        </div>
                        <div class="carousel-item">
                            <img src="../assets/img/boulbasour-unscreen.gif" class="d-block w-100" alt="Bulbasaur">
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample1" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselExample1" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </form>
        </div>
    
        <div class="carru">
            <form>
                <div id="carouselExample2" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="../assets/img/chinito.jpg" class="d-block w-100" alt="Librería El Chinito">
                        </div>
                        <div class="carousel-item">
                            <img src="../assets/img/miss.jpg" class="d-block w-100" alt="Perfumería Miss">
                        </div>
                        <div class="carousel-item">
                            <img src="../assets/img/boulbasour-unscreen.gif" class="d-block w-100" alt="Bulbasaur">
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample2" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselExample2" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </form>
        </div>
    </section>

    <div class="product-list">
        <div class="product">
            <img src="../assets/img/nike.png" alt="Nike Air Jordan">
            <div class="product-info">
                <p>Nike Air Jordan</p>
                <span class="product-price">$50.000</span>
            </div>
            <div class="product-logo">
                <img src="../assets/img/nike-logo.png" alt="Nike Logo">
            </div>
        </div>
        <div class="product">
            <img src="../assets/img/adidas.jpg" alt="Adidas Predator">
            <div class="product-info">
                <p>Adidas Predator</p>
                <span class="product-price">$62.000</span>
            </div>
            <div class="product-logo">
                <img src="../img/adidas-logo.png" alt="Adidas Logo">
            </div>
        </div>
        <div class="product">
            <img src="../assets/img/puma.jpg" alt="Puma X-Ray">
            <div class="product-info">
                <p>Puma X-Ray</p>
                <span class="product-price">$45.000</span>
            </div>
            <div class="product-logo">
                <img src="../assets/img/puma-logo.png" alt="Puma Logo">
            </div>
        </div>
        <div class="product">
            <img src="../assets/img/guantes.png" alt="Guantes Everlast">
            <div class="product-info">
                <p>Guantes Everlast 29oz</p>
                <span class="product-price">$49.000</span>
            </div>
            <div class="product-logo">
                <img src="../assets/img/Everlast_logo.png" alt="Everlast Logo">
            </div>
        </div>
        <div>
            <hr>
        </div>
    </div>
    <footer>
        <img src="../assets/img/home-icon.png" alt="Home">
        <img src="../assets/img/shop-icon.png" alt="Shop">
        <img src="../assets/img/profile-icon.png" alt="Profile">
    </footer>
</body>

</html>
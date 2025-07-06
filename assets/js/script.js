
// Función para mostrar el GIF de carga y redirigir después de 5 segundos
document.getElementById('exitButton').addEventListener('click', function(event) {
    event.preventDefault(); // Evitar la redirección inmediata
    const loadingDiv = document.getElementById('loading');
    loadingDiv.style.display = 'block'; // Mostrar el GIF de carga
    setTimeout(function() {
        window.location.href = 'InicioComercio.php'; // Redirigir después de 5 segundos
    }, 5000);
});
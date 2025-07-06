document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registroForm');  // Aquí defines 'form'
    const checkboxes = document.querySelectorAll('input[name="tipo"]');

    // Asegurarse de que solo un checkbox pueda estar seleccionado
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            checkboxes.forEach(cb => {
                if (cb !== this) {
                    cb.checked = false;
                }
            });
        });
    });

    form.addEventListener('submit', function(event) {
        event.preventDefault(); // Evita el comportamiento por defecto del formulario

        let selectedType = null;
        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                selectedType = checkbox.value;
            }
        });

        if (!selectedType) {
            alert('Por favor, seleccione un tipo de usuario (Cliente o Comercio).');
            return;
        }

        const formData = new FormData(form);
        formData.append('tipo', selectedType);  // Asegurarse de enviar el tipo correcto

        // Enviar los datos al servidor para registrar al usuario
        fetch('registro_handler.php', {
            method: 'POST',
            body: formData,
        })
        .then(response => {
            console.log('Response:', response);  // Agregar este log
            return response.json(); // Intenta parsear el JSON
        })
        .then(data => {
            console.log('Data:', data);  // Verifica qué datos están llegando
            if (data.status === 'success') {
                alert(data.message);
                window.location.href = 'Index.php'; // Redirigir después del registro
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);  // Esto te dará detalles sobre el error
            alert('Ocurrió un error al registrar el usuario.');
        });
    });
    document.getElementById('registroForm').addEventListener('submit', function(event) {
        event.preventDefault();
    
        var formData = new FormData(this);
        var xhr = new XMLHttpRequest();
        xhr.open('POST', this.action, true);
    
        xhr.onload = function() {
            if (xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                console.log('Response:', response);
                if (response.status === 'success') {
                    alert('Usuario registrado exitosamente.');
                } else {
                    alert(response.message);
                }
            } else {
                console.error('Error:', xhr.statusText);
            }
        };
    
        xhr.send(formData);
    });
});

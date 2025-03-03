$(document).ready(function() {
    // Función para mostrar el toast
    function mostrarToast(status) {
        if (status === 'success') {
            toastr.success('¡Registro agregado correctamente!', '', {
                timeOut: 1800 // Duración más larga (en milisegundos)
            }).on('hidden.bs.toast', function() {
                // Desvanecer gradualmente el toast cuando se oculte
                $(this).fadeOut(500);
            });
        } else if (status === 'error') {
            toastr.error('Error al agregar el registro. Por favor, inténtalo de nuevo.', '', {
                timeOut: 1800 // Duración más larga (en milisegundos)
            }).on('hidden.bs.toast', function() {
                // Desvanecer gradualmente el toast cuando se oculte
                $(this).fadeOut(500);
            });
        }
    }

    // Mostrar el toast cuando la página se cargue
    // Obtener el parámetro 'status' de la URL
    var urlParams = new URLSearchParams(window.location.search);
    var status = urlParams.get('status');
    // Mostrar el toast correspondiente
    mostrarToast(status);
});

//Validacion de Campos
function confirmProduction() {
    var productionValue = document.getElementById("productionValue").value.trim();
    if (productionValue === "") {
        toastr.error('Por favor, ingrese la cantidad producida.');
        return false; // Detiene el envío del formulario
    } else {
        // Mostrar alerta de confirmación
        return confirm('¿Está seguro de que desea registrar la cantidad producida?');
    }
}

function cancelProduction() {
    document.getElementById("productionValue").value = ""; // Limpia el campo de cantidad producida
}

function confirmScrapt() {
    var scraptAmount = document.getElementById("scraptAmount").value.trim();
    if (scraptAmount === "") {
        toastr.error('Por favor, ingrese la cantidad de scrap.');
        return false; // Detiene el envío del formulario
    } else {
        // Mostrar alerta de confirmación
        return confirm('¿Está seguro de que desea registrar la cantidad de scrap?');
    }
}

function cancelScrapt() {
    document.getElementById("scraptAmount").value = ""; // Limpia el campo de cantidad de scrap
}
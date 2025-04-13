document.addEventListener('DOMContentLoaded', function() {
    // Configuración de Toastr
    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: "toast-top-right",
        timeOut: 3000
    };

    // Verificar estado
    checkStatus();
});

function checkStatus() {
    fetch('/timeControl/public/getStatus')
        .then(response => response.json())
        .then(data => {
            if (data.status && data.message) {
                const toastrFunction = data.status === "success" ? toastr.success : toastr.error;
                toastrFunction(data.message, '');
            }
        })
        .catch(error => console.error('Error:', error));
}

function confirmWait() {
    return confirm("¿Estás seguro de que deseas poner en espera el trabajo?");
}

function confirmLogout() {
    if (confirm("¿Estás seguro de que deseas cerrar sesión?")) {
        window.location.href = "/timeControl/public/logout";
    }
}
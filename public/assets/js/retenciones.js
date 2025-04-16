// Constantes y variables globales
const TIPOS_DESTINO = {
    VALIDAR: 'produccion_final',
    RETRABAJO: 'retrabajo',
    DESTRUIR: 'destruccion'
};

let retencionActual = null;
let balanceDisponible = 0;

// Función para inicializar el módulo
document.addEventListener('DOMContentLoaded', () => {
    initializeDateTimeUpdate();
    initializeToastr();
});

// Configuración de Toastr
function initializeToastr() {
    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: 'toast-top-right',
        timeOut: 3000
    };
}

// Actualización de fecha y hora
function initializeDateTimeUpdate() {
    const updateDateTime = () => {
        const now = new Date();
        document.getElementById('current-date').textContent = now.toLocaleDateString('es-ES');
        document.getElementById('current-time').textContent = now.toLocaleTimeString('es-ES');
    };
    updateDateTime();
    setInterval(updateDateTime, 1000);
}

// Gestión de modales
function openModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    if (modalId === 'asignarDestinoModal') {
        resetFormularioAsignacion();
    }
}

// Función para abrir el modal de gestión
function openAsignarDestinoModal(retencion) {
    retencionActual = retencion;
    balanceDisponible = parseFloat(retencion.cantidad_disponible);

    // Actualizar información en el modal
    document.getElementById('modalItem').textContent = retencion.item;
    document.getElementById('modalJtWo').textContent = retencion.jtWo;
    document.getElementById('modalCantidadTotal').textContent = `${retencion.cantidad_total} Lb`;
    document.getElementById('modalCantidadDisponible').textContent = `${retencion.cantidad_disponible} Lb`;
    document.getElementById('retencionId').value = retencion.id;

    // Generar campos de destino dinámicamente
    const formContainer = document.querySelector('#asignarDestinoForm .space-y-4');
    formContainer.innerHTML = generarCamposDestino();

    // Agregar event listeners para la validación en tiempo real
    document.querySelectorAll('.destino-cantidad').forEach(input => {
        input.addEventListener('input', validarCantidades);
    });

    openModal('asignarDestinoModal');
}

// Generar campos de destino
function generarCamposDestino() {
    return `
        <div class="bg-yellow-50 p-4 rounded-lg mb-4">
            <p class="text-sm text-yellow-700 font-medium">
                <i class="fas fa-info-circle mr-2"></i>
                Cantidad disponible para asignar: <span id="cantidadRestante">${balanceDisponible}</span> Lb
            </p>
        </div>
        
        ${Object.entries(TIPOS_DESTINO).map(([key, value]) => `
            <div class="border rounded-lg p-4 bg-gray-50">
                <h6 class="font-medium mb-3">${key.charAt(0) + key.slice(1).toLowerCase()}</h6>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Cantidad
                        </label>
                        <input type="number" 
                               name="cantidad_${value}" 
                               class="destino-cantidad w-full px-3 py-2 border border-gray-300 rounded-md"
                               step="0.01" 
                               min="0" 
                               max="${balanceDisponible}"
                               data-tipo="${value}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Motivo
                        </label>
                        <textarea name="motivo_${value}" 
                                 class="w-full px-3 py-2 border border-gray-300 rounded-md"
                                 rows="2"></textarea>
                    </div>
                </div>
            </div>
        `).join('')}
    `;
}

// Validar cantidades en tiempo real
function validarCantidades() {
    let totalAsignado = 0;
    const cantidades = document.querySelectorAll('.destino-cantidad');
    
    cantidades.forEach(input => {
        const cantidad = parseFloat(input.value) || 0;
        totalAsignado += cantidad;
    });

    const cantidadRestante = balanceDisponible - totalAsignado;
    document.getElementById('cantidadRestante').textContent = cantidadRestante.toFixed(2);

    // Validar si se excede la cantidad disponible
    if (totalAsignado > balanceDisponible) {
        toastr.error('La suma de las cantidades excede el total disponible');
        return false;
    }

    return true;
}

// Enviar formulario de asignación
async function submitAsignarDestino(event) {
    event.preventDefault();

    if (!validarCantidades()) {
        return false;
    }

    const formData = new FormData();
    formData.append('retencion_id', retencionActual.id);

    let hayAsignaciones = false;
    Object.values(TIPOS_DESTINO).forEach(tipo => {
        const cantidad = document.querySelector(`[name="cantidad_${tipo}"]`).value;
        const motivo = document.querySelector(`[name="motivo_${tipo}"]`).value;

        if (cantidad && parseFloat(cantidad) > 0) {
            formData.append('destinos[]', JSON.stringify({
                tipo_destino: tipo,
                cantidad: parseFloat(cantidad),
                motivo: motivo
            }));
            hayAsignaciones = true;
        }
    });

    if (!hayAsignaciones) {
        toastr.error('Debe asignar al menos una cantidad a un destino');
        return false;
    }

    try {
        const response = await fetch('/timeControl/public/retencion/asignarDestino', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            toastr.success(result.message);
            closeModal('asignarDestinoModal');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            toastr.error(result.message || 'Error al asignar destinos');
        }
    } catch (error) {
        console.error('Error:', error);
        toastr.error('Error al procesar la solicitud');
    }

    return false;
}

// Resetear formulario
function resetFormularioAsignacion() {
    document.getElementById('asignarDestinoForm').reset();
    retencionActual = null;
    balanceDisponible = 0;
}
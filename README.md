# Manual de Capacitación del Sistema de Control de Tiempos

## Introducción

Este manual describe los principales casos de uso para los operadores y supervisores del Sistema de Control de Tiempos. Este sistema está diseñado para gestionar y registrar eficientemente las actividades y tiempos de los procesos de trabajo en una máquina.

## Registro

### Operadores

**Flujo Básico de Registro:**

1. El operador inicia el navegador.
2. Accede a la página de Login/Registro del sistema.
3. Ingresa los datos requeridos en el formulario, definiendo su área y el tipo de usuario (Operador).
4. El sistema registra al empleado y ahora puede iniciar sesión con su código de empleado y su contraseña.

**Flujo Alternativo:**

- N/A. Cualquier problema al iniciar sesión o registrarse debe ser notificado.

### Supervisores

**Flujo Básico de Registro:**

1. El supervisor inicia el navegador.
2. Accede a la página de Login/Registro del sistema.
3. Ingresa los datos requeridos en el formulario, definiendo su área y el tipo de usuario (Supervisor).
4. El sistema registra al supervisor y ahora puede iniciar sesión con su código de empleado y su contraseña.

**Nota:** Los supervisores deben especificar su área de supervisión durante el registro para acceder a funcionalidades específicas de supervisión.

## Casos de Uso para Operadores

### 1. Seleccionar Máquina

**Actores:** Operador

**Descripción:** Este caso de uso permite al operador seleccionar la máquina con la cual va a trabajar.

**Flujo Básico:**

1. Accede al sistema e inicia sesión con sus datos.
2. Según su tipo de usuario, será dirigido a seleccionar una máquina de entre todas las de su área.
3. El sistema registra la máquina seleccionada y redirige a la sección "Ingresar Datos de Operaciones".

**Flujo Alternativo:**

- N/A. Cualquier problema debe ser notificado.

### 2. Ingresar Datos de Operaciones

**Actores:** Operador

**Descripción:** Este caso de uso permite ingresar un ITEM y un JOB TICKET (JT/WO).

**Flujo Básico:**

1. Ingresar ITEM y JT/WO a trabajar.
2. El sistema redirige a la página de Control de Tiempos.

**Flujo Alternativo:**

- N/A. Cualquier problema debe ser notificado.

### 3. Control de Tiempos

**Actores:** Operador

**Descripción:** Aquí es donde se registran los tiempos de las acciones que se dan durante el trabajo.

**Flujo Básico:**

1. La página de Control de Tiempos consta de los siguientes botones:
   - **Preparación:** Para preparar la máquina.
   - **Producción:** Para iniciar la producción.
   - **Contratiempos:** Para registrar un imprevisto. Dependiendo de lo ocurrido, se debe seleccionar entre los contratiempos que están en el botón.
   - **Asignar Velocidad:** Para registrar la velocidad de producción. Solo puede ser definida cuando se encuentra en Producción.
   - **Fin:** Para finalizar el trabajo. Se piden los valores de la cantidad de Scrap y cantidad Producida.
2. Debajo de los botones de acción hay una lista de solo visualización que muestra los casos del botón de Preparación.
3. El botón de Fin marca el final del trabajo en la máquina con los datos de Operaciones (ITEM, JT/WO). Envía a la sección de Seleccionar Máquina.

**Flujo Alternativo:**

- **Entrega Parcial:** Este botón se utiliza cuando se tienen las cantidades de Scrap y Producción, pero se va a seguir trabajando con el mismo ITEM y JT/WO.

**Resumen de Entrega Parcial:**

- La página también consta de un Resumen de entrega parcial donde se pueden ver los totales de las cantidades de Producción y Scrap que se han registrado como parciales.

## Casos de Uso para Supervisores

### 1. Operaciones Abiertas

**Actores:** Supervisor

**Descripción:** Muestra al supervisor lo que están haciendo los operadores, en qué máquina y con qué ITEM, y el tiempo que tienen en una determinada operación.

**Flujo Básico:**

1. El supervisor inicia sesión en el sistema.
2. Accede a la sección de "Operaciones Abiertas".
3. Visualiza la lista de operaciones actuales de los operadores, incluyendo la máquina, el ITEM, y el tiempo en cada operación.

### 2. Producción y Scrap

**Actores:** Supervisor

**Descripción:** Muestra al supervisor las entregas de producción y scrap de cada máquina, indicando qué operador realizó cada acción y a qué hora.

**Flujo Básico:**

1. El supervisor inicia sesión en el sistema.
2. Accede a la sección de "Producción y Scrap".
3. Visualiza un resumen de producción y scrap de cada máquina, incluyendo los detalles del operador y la hora de cada entrega.

**Nota:** Toda la información de la sección del supervisor es del día actual.

---

Si tienes alguna pregunta o necesitas más detalles sobre el uso del sistema, no dudes en preguntar.

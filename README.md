# Manual de Capacitación del Sistema de Control de Tiempos

## Introducción

Este manual describe los principales casos de uso para los operadores y supervisores del Sistema de Control de Tiempos. Este sistema está diseñado para gestionar y registrar eficientemente las actividades y tiempos de los procesos de trabajo en una máquina.

## Login 

**Actores:** Operador, Supervisor, Calidad (QA)

**Flujo Básico de Registro:**

1. El operador, supervisor y calidad (QA), inicia el navegador.
2. Accede a la página de Login/Registro del sistema.
3. Ingresa los datos requeridos en el formulario, definiendo su área y el tipo de usuario (Operador, Supervisor, Calidad (QA)).
4. El sistema registra al empleado y ahora puede iniciar sesión con su código de empleado y su contraseña.

## Forgot password

**Actores:** Operador, Supervisor, Calidad (QA)

1. El operador, supervisor y calidad (QA), inicia el navegador.
2. Accede a la página de Login/Registro del sistema.
3. En la seccion de login se encuentra un apartado para cambiar la contraseña o forgot password.
4. El usuario accede a forgot password y debe de ingresar su codigo, contraseña nueva y confirmarla.




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

### Calidad (QA)

**Flujo Básico de Registro:**

1. El QA inicia el navegador.
2. Accede a la página de Login/Registro del sistema.
3. Ingresa los datos requeridos en el formulario, definiendo su área y el tipo de usuario (QA).
4. El sistema registra al QA y ahora puede iniciar sesión con su código de empleado y su contraseña.

**Nota:** Los QA deben especificar su área de calidad durante el registro para acceder a funcionalidades específicas de calidad.

### Planificador

**Flujo Básico de Registro:**

1. El Planificador inicia el navegador.
2. Accede a la página de Login/Registro del sistema.
3. Ingresa los datos requeridos en el formulario, definiendo su área y el tipo de usuario (Planificador).
4. El sistema registra al Planificador y ahora puede iniciar sesión con su código de empleado y su contraseña.

**Nota:** Los Planificador deben especificar su área de calidad durante el registro para acceder a funcionalidades específicas de Planificador.

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

- **Solicitud de correcion:**  Esta solicitud de correcion ocurre cuando el supervisor solicita una correcion o revision de la entraga que hizo el operador, entonces el operador en este apartado corregiria la entrega que hizo mal.


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

### 3. Validaciones de entrega

**Actores:** Supervisor

**Descripción:** Muestra al supervisor las entregas que hacen los operadores, indicando los produccion y scrap, item, jt/wo, maquina que opera, cliente y dandole la opcion de poder validar y revisar estas entregas.

**Flujo Básico:**
1. Accede y Visualiza a la seccion de validacion de produccion y scrap, incluyendo detalles de cantidad de produccion y scrap, item, jt/wo, cliente, codigo empleado, maquina y opcciones para validar y revisar estas entregas.
2. El supervisor tiene la opcion de validar. La opcion de validar es cuando el supervisor verifica que la entrega de el operador es validar y cuando la valida, esto se enviaria a QA.
3. El supervisor tiene la opcion de revisar. La opcion de revisar es cuando el supervisor verifica que la entrega que hizo el operador no era la cantidad que tenia que producir, entonces el supervisor usaria esta opcion en este tipo de caso y al operador se le enviaria una notificacion. 

### 4. Revisiones pendientes

**Actores:** Supervisor

**Descripción:** Muestra al supervisor las entregas que este envio a revisar independientemente del motivo de esta.

**Flujo Básico:**
1. Accede y Visualiza a la seccion de reviciones pendientes.
2. El supervisor puede visualizar las revisiones que envio al operador.
3. El supervisor puede ver el motivo de esta revision y puede cancelarla, en caso de que ese se haya equivocado. 

## Casos de Uso para QA (Calidad)

### 1. Panel de Control QA

**Actores:** QA

**Descripción:** Esta seccion muestra un breve resumen del la estadisticas de validacion de entregas (pendiente, produccion, scrap), Accion QA donde se puede ver la entregas que se han validado por el supervisor de area y QA, se muestra un resumen de las retenciones, revisiones pendientes y las ultimas validaciones que se han hecho.

**Flujo Básico:**

1. El QA inicia seccion en el sistema.
2. Accede a la sección de Dashboard.
3. Visualiza la estadisticas de de las secciones de validacion de entregas, accion QA, renteciones, revisiones pendientes y ultimas validaciones de entrega.

### 2. Validaciones de entrega

**Actores:** QA

**Descripción:** Muestra al QA las entregas que hacen los operadores, indicando los produccion y scrap, item, jt/wo, maquina que opera, cliente y dandole la opcion de poder validar y revisar estas entregas.

**Flujo Básico:**

1. Accede y Visualiza a la seccion de validacion de produccion y scrap, incluyendo detalles de cantidad de produccion y scrap, item, jt/wo, cliente, codigo empleado, maquina y opcciones para validar y revisar estas entregas.
2. El supervisor tiene la opcion de validar. La opcion de validar es cuando el supervisor verifica que la entrega de el operador es validar y cuando la valida, esto se enviaria a QA.
3. El supervisor tiene la opcion de revisar. La opcion de revisar es cuando el supervisor verifica que la entrega que hizo el operador no era la cantidad que tenia que producir, entonces el supervisor usaria esta opcion en este tipo de caso y al operador se le enviaria una notificacion.

### 3. Accion QA

**Actores:** QA

**Descripción:** Muestra al QA las entregas que han sido validadas tanto de produccion o scrap por el supervisor o el mismo QA de cada area, donde se puede visualiza estas entregas y el QA tiene la opcion de validar que esa entrega cumpla con los estandares de calidad o puede retenerlo.

**Flujo Básico:**

1. Accede a la seccion de Accion QA
2. Visualiza las entregas que han sido validadas por el supervisor o el mismo QA.
3. Opcion de validar que estas entregas cumplen con los estandares de calidad.
4. Opcion de retener esta entra si no cumple con los estandares de calidad. 

### 4. Retencion 

**Actores:** QA

**Descripción:** Muesta al QA las entregas que han sido retenidas por cualquier motivo, donde se puede gestionar esta y tomar decisiones basadas en el estado de la entrega.

**Flujo Básico:**

1. Accede a la seccion de retenciones
2. Visualiza las entregas que han sido retenidas por el QA.
3. Opcion de gestionar las entregas que han sido retenidas.
4. En la opcion de gestionar se visualiza un modal con las opciones de liberar a Producción, retrabajo o destruir.

### 4. Retencion 

**Actores:** QA

**Descripción:** Muesta al QA las entregas que han sido retenidas por cualquier motivo, donde se puede gestionar esta y tomar decisiones basadas en el estado de la entrega.

**Flujo Básico:**

1. Accede a la seccion de retenciones
2. Visualiza las entregas que han sido retenidas por el QA.
3. Opcion de gestionar las entregas que han sido retenidas.
4. En la opcion de gestionar se visualiza un modal con las opciones de liberar a Producción, retrabajo o destruir.

### 4. Revisiones pendientes  

**Actores:** QA

**Descripción:** Muesta al QA las reviones pendientes de las entregas que este envio a corregir o revisar.

**Flujo Básico:**

1. Accede a la seccion de reviones pendientes
2. Visualiza las entregas que han sido envia a revision.

### 4. Reporte de entrega (Produccion)

**Actores:** QA

**Descripción:** Muesta al QA las entregas de produccion que han pasado todo el proceso de validacion y crea un reporte de con los detalles de esta.

**Flujo Básico:**

1. Accede a la seccion de reporte de entrega
2. Visualiza las entregas que han pasado todo el proceso de validacion.
3. Opcion para poder visualizar el detalle de esta entregas y generar su reporte.
4. Opcion en la parte del detalle del reporte de editar paletas, cajas y piezas.
5. Opcion de imprimir todas las hoja.

**Nota:** Toda la información de la sección del supervisor es del día actual.

### 5. Reporte de scrap

**Actores:** QA

**Descripción:** Muesta al QA las entregas de scrap han pasado todo el proceso de validacion y crea un reporte de con los detalles de esta.

**Flujo Básico:**

1. Accede a la seccion de reporte de scrap.
2. Visualiza las entregas de scrap que han pasado todo el proceso de validacion.
3. Opcion para poder visualizar el detalle de esta entregas y generar su reporte.
4. Opcion de imprimir todas las hoja.

**Nota:** Toda la información de la sección del supervisor es del día actual.


## Casos de Uso para Planificador

### 1. Agenda Planificador

**Actores:** Planificador

**Descripción:** Muertra al planificador un calendario donde este puede gestionar las ordenes, tambien puede crear nuevas ordenes y darles una distribuccion a la cantidad requeridad de esa orden. 

**Flujo Básico:**

1. Accede a la seccion de Gestion de produccion.
2. Visualiza un calendario con los orden pendiente o en produccion.
3. Opcion para agregar nuevas ordenes y darle una distribuccion.





## Backup Database control

**Nota:** En relacion con el backend de la base de datos se esta haciendo uno manual diario de la base de datos control en phpmyadmin y se esta guardando en esta ruta de archivo C:\Users\eduardofd\Documents\Backend-Control


---

Si tienes alguna pregunta o necesitas más detalles sobre el uso del sistema, no dudes en preguntar.

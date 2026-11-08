# Base para el manual de usuario de SIRSE USAC

Las marcas `[CAPTURA: ...]` indican dónde insertar una imagen. Toma las capturas con datos de demostración.

## 1. Primeros pasos

### Requisitos

- Navegador actualizado: Chrome, Firefox o Edge.
- Conexión a Internet y un correo electrónico activo.
- Estudiantes: registro académico y DPI.

### Tipos de usuario

| Rol | Qué puede hacer | Cómo obtiene su cuenta |
|---|---|---|
| Estudiante | Registrar su EPS, enviarlo a aprobación y ver lo que consignó | Verificando registro académico y DPI |
| Unidad académica | Revisar, aprobar y consultar los EPS de su alcance | La crea DIGEU o la cuenta principal de su unidad |
| DIGEU | Administrar el sistema y consultar todo | La crea o asigna DIGEU |
| Invitado | Ver panel y documentación; convertirse en estudiante | Registro público |

### Iniciar sesión

1. Abre el sistema y selecciona la opción para iniciar sesión.
2. Escribe tu correo y contraseña.
3. Si tienes doble factor, escribe el código de tu aplicación o un código de recuperación.

[CAPTURA: inicio de sesión]

### Recuperar la contraseña

1. En inicio de sesión, usa la opción de contraseña olvidada.
2. Escribe tu correo.
3. Abre el enlace recibido y define una contraseña nueva.

En producción, la contraseña debe tener al menos 12 caracteres, con mayúsculas, minúsculas, números y símbolos, y no debe aparecer en filtraciones conocidas.

### Ajustes de la cuenta

En **Ajustes** puedes:

- Cambiar nombre, correo y contraseña.
- Activar el doble factor. Guarda los códigos de recuperación en un lugar seguro.
- Elegir la apariencia.
- Eliminar tu cuenta, si el sistema lo permite para tu perfil.

## 2. Manual del estudiante

### 2.1 Activar tu acceso

Solo se hace una vez.

1. Entra a la opción **Soy estudiante**.
2. Escribe tu registro académico (de 6 a 12 dígitos) y tu DPI (13 dígitos).
3. Si los datos coinciden con Registro y Estadística:
   - Sin cuenta: escribe tu correo y contraseña para crearla.
   - Con cuenta de invitado: tu cuenta pasa a ser de estudiante.
   - Con cuenta de estudiante: inicia sesión con tu correo y contraseña.

El DPI se usa para verificar tu identidad y no se almacena.

[CAPTURA: verificación de estudiante]

**Verificar tu correo.** Al crear tu cuenta recibes un enlace de verificación para confirmar tu dirección. Si no lo recibiste, pide el reenvío en la pantalla de verificación.

**Si te equivocaste de correo.** Pide a DIGEU que lo corrija. El enlace llegará a la nueva dirección. Si además olvidaste tu contraseña, restablécela con ese correo en **¿Olvidaste tu contraseña?**.

### 2.2 Elegir la carrera

1. En **Mis carreras y EPS** verás tus carreras según Registro y Estadística.
2. Elige la carrera en la que realizas tu EPS.
3. El sistema toma tu unidad académica de esa carrera.

Cada carrera tiene un expediente propio. Puedes salir y retomar donde lo dejaste.

[CAPTURA: mis carreras]

### 2.3 Indicar el programa

En el primer paso, activa **Programa de tu EPS** si tu EPS pertenece al EPSUM. Si no lo activas, queda como EPS normal.

### 2.4 Llenar los seis ejes

En cada eje puedes agregar varios registros, editarlos o eliminarlos. Se guardan al instante. Las fechas no pueden ser futuras.

#### Eje 1. Bienes y servicios

Registra lo que entregaste o prestaste a la comunidad.

| Campo | Obligatorio | Nota |
|---|---|---|
| Bien o servicio del catálogo | Según la pantalla | Filtra por línea de intervención |
| Tipo | Sí, si no eliges del catálogo | Bien o servicio |
| Descripción | Sí | Qué se realizó |
| Cantidad | No | De 1 a 1,000,000. Se usa para calcular el aporte |
| Beneficiarios (descripción) | No | Quiénes se beneficiaron |
| Cantidad de beneficiarios | No | Número estimado de personas |
| Poblaciones vulnerables | No | Marca todos los grupos que correspondan |
| Fecha | Sí | |

[CAPTURA: eje de bienes y servicios]

#### Eje 2. Publicaciones de investigación

| Campo | Obligatorio | Nota |
|---|---|---|
| Título | Sí | |
| Tipo | Sí | Artículo, libro, capítulo, tesis, informe, ponencia u otro |
| Autores | Sí | |
| Medio de publicación | No | |
| Resumen | No | |
| Enlace | No | Debe ser una dirección web válida |
| Fecha de publicación | No | |

#### Eje 3. Transferencia de conocimiento

| Campo | Obligatorio | Nota |
|---|---|---|
| Acción del catálogo | No | |
| Tipo de actividad | Sí | Capacitación, taller, asesoría, documento o material, u otra |
| Actividad | Sí | |
| Comunidad | Sí | |
| Número de participantes | No | |
| Fecha | Sí | |

#### Eje 4. Territorio

| Campo | Obligatorio | Nota |
|---|---|---|
| Departamento | Sí | |
| Municipio | Sí | Debe pertenecer al departamento |
| Comunidad | No | |
| Punto en el mapa | No | Sugiere departamento y municipio |
| Referencia | No | |

La primera ubicación registrada es la que se usa en las estadísticas. Regístrala primero si tienes varias.

[CAPTURA: eje de territorio y mapa]

#### Eje 5. Actores y participantes

**Primera parte (obligatoria dentro del registro):**

- Institución receptora: dónde realizas tu EPS.
- Contraparte.
- Comunidad beneficiada.

**Segunda parte (instituciones aliadas):**

1. Agrega las instituciones que participaron o cooperaron.
2. Elige cada una del catálogo. Una misma institución no se puede repetir en un EPS.
3. Describe su aporte, si quieres.

Si no aparece, pide a DIGEU que la agregue.

#### Eje 6. Seguimiento e impacto

| Campo | Obligatorio | Nota |
|---|---|---|
| Tipo de registro | Sí | Avance parcial o evaluación final de impacto |
| Indicador | Sí | |
| Avance y porcentaje | No | Porcentaje de 0 a 100 |
| Cumplimiento | No | Cumplido, parcial o no cumplido |
| Observaciones | No | |
| Evaluación de impacto | Sí, en evaluación final | |
| Fecha | Sí | |

#### ¿Dónde va cada cosa?

| Lo que hiciste | Dónde se registra |
|---|---|
| Biblioteca, filtros de agua, jornada de salud | Bienes y servicios |
| Capacitación, taller, asesoría, manual o propuesta para transferir conocimiento | Transferencia |
| Artículo, tesis o informe de investigación | Publicaciones |

### 2.5 Cerrar y enviar tu EPS

1. En **Cierre**, revisa el resumen y corrige lo necesario.
2. Revisa tu **indicador de articulación**.
3. Sube tu mejor foto: JPG, PNG o WebP, hasta 5 MB y al menos 300 × 200 píxeles.
4. Si la tienes, sube la orden de impresión: PDF, JPG o PNG, hasta 10 MB. Es opcional.
5. Selecciona **Enviar mi EPS a aprobación**. Necesitas al menos un registro en los ejes.
6. El sistema muestra el lugar de tu solicitud en la bandeja.

**Revisa bien antes de enviar.** Puedes corregir mientras tu EPS esté en progreso o en revisión. Cuando esté **Aprobado**, solo podrás ver lo que consignaste. Si hay que corregir algo, pide a tu supervisión que retire la aprobación.

[CAPTURA: cierre del EPS]

### 2.6 Estados

| Estado | Qué significa |
|---|---|
| En progreso | Todavía lo estás llenando |
| En revisión | Está en la bandeja de supervisión |
| Aprobado | Aparece en repositorio y cuenta en estadísticas. Solo se puede ver, no modificar |

## 3. Manual de la unidad académica

### 3.1 Tu acceso

1. DIGEU o la cuenta principal de tu unidad crean tu cuenta y te asignan una unidad y, si corresponde, carreras.
2. Recibirás un correo para definir tu contraseña.
3. Si tienes carreras asignadas, solo verás esas carreras. Si no, verás toda la unidad y serás la cuenta principal (ver 3.6).

### 3.2 Bandeja de solicitudes

1. Abre **Bandeja de solicitudes**. El menú indica cuántas esperan.
2. Atiende primero la de arriba, que es la más antigua.
3. Revisa turno, fecha, hora y días de espera.
4. Abre la solicitud para revisar el EPS.

[CAPTURA: bandeja de solicitudes]

### 3.3 Revisar y aprobar

1. Revisa los seis ejes y descarga la orden de impresión si existe.
2. Selecciona **Aprobar EPS**.
3. Marca el acepto: confirmas que lo descrito está comprobado y que se ejecutó.
4. Confirma la aprobación.

La aprobación queda en la bitácora. El EPS pasa a **Aprobado**.

Solo puedes aprobar un EPS que el estudiante ya haya enviado. Mientras está aprobado, el estudiante no puede cambiar nada. Si hay que corregirlo, retira la aprobación: el EPS vuelve a **En revisión**, deja de contar en resultados y el estudiante puede editarlo de nuevo.

[CAPTURA: aprobación con acepto]

### 3.4 Estudiantes y EPS

En **Estudiantes y EPS** puedes buscar por nombre, carné o carrera, y filtrar por estado. Los EPS del EPSUM llevan una etiqueta.

### 3.5 Consultas

En estadísticas, ODS, repositorio e investigaciones verás el aviso **Solo ves los EPS de...**. Tus exportaciones respetan el mismo alcance.

### 3.6 Supervisores y carreras (cuenta principal)

La cuenta principal de cada unidad es la que no tiene carreras asignadas. En su menú aparecen **Supervisores** y **Carreras**.

**Crear un supervisor**

1. Abre **Supervisores** y selecciona **Crear supervisor**.
2. Escribe el nombre y el correo.
3. Marca las carreras que verá y aprobará. Debes marcar al menos una.
4. La persona recibe un correo para definir su contraseña.

Con **Editar acceso** cambias sus carreras y con **Cambiar correo** corriges su correo. Solo ves a los supervisores de tu unidad.

**Agregar carreras**

Las carreras de tu unidad se agregan solas cuando un estudiante elige la carrera de su EPS. Para agregar otra:

1. Abre **Carreras** y escribe el carné de un estudiante de esa carrera.
2. Marca las carreras nuevas y selecciona **Agregar las seleccionadas**.

Solo aparecen y se pueden agregar las carreras de tu unidad. Para eliminar una carrera, pídelo a DIGEU.

[CAPTURA: supervisores de la unidad]

## 4. Manual de DIGEU

### 4.1 Crear una cuenta

1. Abre **Usuarios** y selecciona **Crear cuenta**.
2. Escribe nombre y correo.
3. Elige el rol.
4. Para una unidad académica, elige la unidad y, si quieres, sus carreras.
5. Guarda. La persona recibe un correo para definir su contraseña.

### 4.2 Cambiar el acceso

1. En **Usuarios**, selecciona **Editar acceso**.
2. Elige Invitado, Unidad académica o DIGEU.
3. Actualiza la unidad y las carreras, si corresponde.

No puedes editar tu propio acceso ni el de un estudiante.

### Corregir un correo

Sirve también para cuentas de estudiante.

1. En **Usuarios**, busca a la persona y selecciona **Cambiar correo**.
2. Escribe el correo correcto y guarda.
3. El sistema envía el enlace de verificación a la nueva dirección. Mientras tanto, la cuenta muestra **Correo sin verificar**.
4. Si la persona olvidó su contraseña, indícale que use **¿Olvidaste tu contraseña?** con el correo nuevo.

Si escribes el mismo correo en una cuenta sin verificar, el enlace se envía de nuevo. Tu propio correo se cambia en **Ajustes**.

[CAPTURA: usuarios y cambio de correo]

### 4.3 Manejo de datos

| Catálogo | Uso | Regla al eliminar |
|---|---|---|
| Bienes y servicios | Eje 1, con valor base y anclajes | Si está en uso, se desactiva |
| Acciones de transferencia | Eje 3 | Si está en uso, se desactiva |
| Líneas, políticas, áreas y ODS | Clasifican bienes y servicios | Si tienen elementos anclados, no se eliminan |
| Poblaciones vulnerables | Eje 1 | Si está en uso, se desactiva |
| Instituciones aliadas | Eje 5 | Si figura en un EPS, no se elimina |
| Departamentos y municipios | Eje 4 | Con EPS, no se eliminan |
| Unidades académicas | Expedientes y usuarios | Con EPS, carreras o usuarios, se desactiva |
| Carreras | Supervisores | Si está asignada, no se elimina |

**Ajustar el valor base:** edita el bien o servicio y escribe el valor en quetzales. Esto cambia el aporte calculado de los EPS que usan ese elemento, incluidos los ya aprobados.

**Agregar carreras:**

1. Abre **Carreras** y escribe un carné.
2. Consulta el registro académico.
3. Agrega las carreras nuevas. Las existentes aparecen marcadas.

[CAPTURA: manejo de datos]

### 4.4 Bitácora

Filtra por usuario o correo, módulo, tipo de acción y fechas. Registra cambios, accesos, solicitudes, aprobaciones, retiros y exportaciones. Nadie puede editarla ni eliminarla desde el sistema.

[CAPTURA: bitácora]

## 5. Consultas (DIGEU y unidad académica)

Los estudiantes no tienen acceso a las consultas: solo ven su propio EPS.

### Estadísticas

1. Entra a **Estadísticas**.
2. Elige la métrica: investigaciones, bienes y servicios, aporte, beneficiarios, acciones, participantes, instituciones, estudiantes o EPS.
3. Filtra por año, rango de fechas (**Desde** y **Hasta**, en formato dd-mm-aaaa), unidad, carrera y eje.
4. Selecciona un departamento en el mapa y usa **Ver más** para abrir su detalle por municipio.
5. Usa **Generar PDF** o **Exportar Excel**.

[CAPTURA: mapa de estadísticas]

### ODS

1. Entra a **ODS**.
2. Revisa aporte, bienes y servicios, beneficiarios y EPS por objetivo.
3. Puedes usar los mismos filtros que en estadísticas.
4. Elige un ODS para ver sus bienes, líneas, áreas y unidades.
5. Usa **Exportar Excel**.

[CAPTURA: pantalla de ODS]

### Repositorio

1. Entra a **Repositorio**.
2. Busca por estudiante o carrera.
3. Filtra por unidad o población vulnerable.
4. Abre un EPS para ver lo registrado y su indicador de articulación.

[CAPTURA: repositorio]

### Investigaciones

Busca por título, autores, resumen o carrera, y filtra por tipo y unidad.

### Cómo leer los datos

- Solo se cuentan EPS aprobados.
- El año y las fechas son los de la orden de impresión o, si no existe, los de la aprobación.
- Al elegir un eje, solo cuentan los EPS con registros en ese eje.
- El aporte es una estimación: cantidad por valor base.
- Participantes y beneficiarios no se suman entre sí.
- Un EPS se ubica en el mapa según su primera ubicación.

## 6. Invitado

Por ahora, un invitado no accede a las consultas. Si eres estudiante en EPS, usa **Soy estudiante**. Si necesitas otro acceso, solicítalo a DIGEU.

## 7. Problemas frecuentes

| Problema o mensaje | Qué hacer |
|---|---|
| El registro académico y el DPI no coinciden | Revisa los números. Si siguen sin coincidir, consulta con Registro y Estadística |
| No pudimos consultar el registro académico | Intenta de nuevo en unos minutos |
| Demasiados intentos | Espera un minuto antes de volver a intentar |
| Tu cuenta ya está activada | Inicia sesión con tu correo y contraseña |
| Este registro académico ya está vinculado a otra cuenta | Contacta a DIGEU |
| Registra al menos un elemento | Agrega un registro en algún eje antes de enviar |
| No aparece una institución aliada | Pide a DIGEU que la agregue |
| No aparece un bien o servicio | Cambia de línea de intervención o avisa a DIGEU |
| Me pide verificar el correo | Abre el mensaje o solicita el reenvío. Revisa también spam |
| Me equivoqué de correo | Pide a DIGEU que lo corrija en **Usuarios** |
| Tu EPS ya está aprobado: solo puedes ver lo que registraste | Pide a tu supervisión que retire la aprobación si hay que corregir algo |
| La fecha final no puede ser anterior a la inicial | Corrige **Desde** o **Hasta** en los filtros |
| No tienes permiso para acceder a esta sección | Tu rol no tiene acceso. Consulta con DIGEU |
| Mi EPS no aparece en estadísticas | Confirma que esté **Aprobado** y revisa los filtros |
| El aporte aparece en Q0 | El bien no tiene valor base o no se eligió del catálogo |
| El mapa se ve con puntos | El mapa de Google no está disponible; se muestra el alterno |

## 8. Soporte

Contacto de soporte de DIGEU: [PENDIENTE].

# Casos de Uso — SIRSE-EPS / USAC

**Sistema Unificado de Registro y Seguimiento del Ejercicio Profesional Supervisado**

Actores del sistema:
- **Estudiante** — usuario que registra su información de EPS
- **Unidad Académica** — coordinador/administrador de facultad, escuela o centro universitario
- **DIGEU** — administrador central (Dirección General de Extensión Universitaria)
- **Sistema** — procesos automáticos (validaciones, bitácora, reportes)

---

## Mes 1 — Parte administrativa

### CU-01 · Autenticación y control de acceso por roles

| Campo | Detalle |
|---|---|
| **Actor(es)** | Estudiante, Unidad Académica, DIGEU |
| **Descripción** | Permite a cualquier usuario autenticarse en el sistema y acceder únicamente a las funciones correspondientes a su nivel de acceso (estudiante / unidad académica / DIGEU). |
| **Precondiciones** | El usuario debe tener una cuenta previamente creada en el sistema con un rol asignado. |
| **Flujo principal** | 1. El usuario ingresa a la pantalla de login.<br>2. Introduce usuario y contraseña.<br>3. El sistema valida las credenciales.<br>4. El sistema identifica el rol del usuario.<br>5. El sistema redirige al panel correspondiente (estudiante, unidad académica o DIGEU), mostrando solo los módulos permitidos por el middleware de permisos. |
| **Flujos alternativos** | 3a. Credenciales incorrectas → el sistema muestra mensaje de error y permite reintentar.<br>4a. Usuario sin rol asignado → el sistema bloquea el acceso y notifica al administrador. |
| **Postcondiciones** | El usuario queda autenticado con una sesión activa y acceso restringido a su rol. |

---

### CU-02 · Gestión de Unidades Académicas

| Campo | Detalle |
|---|---|
| **Actor(es)** | DIGEU |
| **Descripción** | Permite crear, consultar, actualizar y eliminar (CRUD) facultades, escuelas y centros universitarios, así como asignar administradores a cada unidad. |
| **Precondiciones** | El usuario DIGEU debe estar autenticado. |
| **Flujo principal** | 1. DIGEU accede al módulo de Unidades Académicas.<br>2. Selecciona "Nueva unidad académica".<br>3. Ingresa nombre, tipo (facultad/escuela/centro) y datos de contacto.<br>4. Asigna un usuario administrador a la unidad.<br>5. El sistema guarda la unidad y notifica al administrador asignado. |
| **Flujos alternativos** | 3a. Datos incompletos → el sistema muestra validaciones de campo.<br>5a. DIGEU edita o desactiva una unidad existente en vez de crear una nueva. |
| **Postcondiciones** | La unidad académica queda registrada y disponible para la asignación de estudiantes. |

---

### CU-03 · Gestión de Estudiantes / EPS

| Campo | Detalle |
|---|---|
| **Actor(es)** | Unidad Académica, Estudiante |
| **Descripción** | Permite registrar estudiantes, asignarlos a un programa (EPS facultativo o EPSUM) y mantener su expediente y perfil dentro del sistema. |
| **Precondiciones** | La unidad académica debe existir previamente en el sistema. |
| **Flujo principal** | 1. La unidad académica accede al módulo de estudiantes.<br>2. Registra al estudiante con sus datos generales.<br>3. Asigna el programa correspondiente (EPS facultativo o EPSUM).<br>4. El sistema crea el expediente del estudiante.<br>5. El estudiante recibe acceso a su perfil para completar información y formularios. |
| **Flujos alternativos** | 2a. El estudiante ya existe → el sistema evita duplicados y sugiere actualizar el registro existente.<br>3a. Estudiante inscrito en EPSUM → el sistema habilita los campos específicos del programa multiprofesional. |
| **Postcondiciones** | El estudiante queda registrado, asignado a un programa y habilitado para llenar los formularios de ingreso. |

---

### CU-04 · Diligenciamiento de Formularios de Ingreso

| Campo | Detalle |
|---|---|
| **Actor(es)** | Estudiante |
| **Descripción** | Permite al estudiante completar los seis formularios de ingreso definidos institucionalmente, con validaciones por campo, guardado parcial y adjuntos. |
| **Precondiciones** | El estudiante debe estar registrado y autenticado en el sistema. |
| **Flujo principal** | 1. El estudiante accede a la sección de formularios.<br>2. Selecciona el formulario a completar.<br>3. Llena los campos requeridos y adjunta documentos de respaldo si aplica.<br>4. Guarda el avance de forma parcial en cualquier momento.<br>5. Envía el formulario como definitivo una vez completo.<br>6. El sistema valida los campos obligatorios y confirma el envío. |
| **Flujos alternativos** | 4a. El estudiante abandona el formulario sin finalizar → el sistema conserva el progreso guardado.<br>6a. Campos incompletos o inválidos al enviar → el sistema bloquea el envío y señala los errores. |
| **Postcondiciones** | El formulario queda registrado en el expediente del estudiante y disponible para consulta por la unidad académica y DIGEU. |

---

## Mes 2 — Los seis ejes

### CU-05 · Registro de Bienes y Servicios Generados (Eje 1)

| Campo | Detalle |
|---|---|
| **Actor(es)** | Estudiante, Unidad Académica |
| **Descripción** | Permite registrar y consultar los bienes y servicios producidos por el estudiante durante el ejercicio de su EPS. |
| **Precondiciones** | El estudiante debe tener su expediente y formularios de ingreso completos. |
| **Flujo principal** | 1. El estudiante accede al módulo del Eje 1.<br>2. Registra el bien o servicio generado (tipo, descripción, beneficiarios, fecha).<br>3. Adjunta evidencia si corresponde.<br>4. El sistema guarda el registro vinculado al expediente del estudiante.<br>5. La unidad académica puede consultar los registros de sus estudiantes. |
| **Flujos alternativos** | 2a. Registro recurrente (varios bienes/servicios) → el estudiante repite el proceso por cada uno. |
| **Postcondiciones** | El bien o servicio queda registrado y disponible para consolidación por DIGEU. |

---

### CU-06 · Registro de Publicaciones de Investigación (Eje 2)

| Campo | Detalle |
|---|---|
| **Actor(es)** | Estudiante, Unidad Académica |
| **Descripción** | Permite registrar investigaciones y publicaciones vinculadas al ejercicio profesional supervisado. |
| **Precondiciones** | El estudiante debe tener su expediente activo. |
| **Flujo principal** | 1. El estudiante accede al módulo del Eje 2.<br>2. Ingresa los datos de la investigación o publicación (título, tipo, autores, medio de publicación).<br>3. Adjunta el documento o enlace correspondiente.<br>4. El sistema guarda el registro.<br>5. La unidad académica valida o consulta el registro. |
| **Flujos alternativos** | 3a. No existe documento adjunto disponible → el estudiante puede completar el registro y adjuntarlo posteriormente. |
| **Postcondiciones** | La publicación queda registrada y vinculada al expediente del estudiante. |

---

### CU-03b · Solicitud de aprobación sin orden de impresión (extensión)

| Campo | Detalle |
|---|---|
| **Actor(es)** | Estudiante, Unidad Académica, DIGEU |
| **Descripción** | Un estudiante que no tiene orden de impresión (por ejemplo, porque no hay un informe escrito) envía su EPS a aprobación de su unidad académica, que lo atiende desde una bandeja de solicitudes en orden de llegada. |
| **Precondiciones** | El EPS está en progreso, no tiene orden de impresión y tiene al menos un registro en los ejes. |
| **Flujo principal** | 1. El estudiante envía su EPS a aprobación desde el último paso.<br>2. El sistema registra la fecha y hora de llegada y anota el envío en la bitácora.<br>3. La solicitud aparece en la bandeja de la unidad (y de DIGEU), con el que llegó primero arriba.<br>4. La unidad revisa el EPS y lo aprueba confirmando el acepto de que lo descrito está comprobado y se ejecutó.<br>5. La solicitud sale de la bandeja y el EPS queda verificado. |
| **Flujos alternativos** | 1a. El EPS ya fue enviado, ya está completo o aprobado, o no tiene registros → el sistema no permite el envío. |
| **Postcondiciones** | El EPS queda verificado, publicado en el repositorio y cuenta para las estadísticas. |

---

### CU-07 · Registro de Transferencia de Conocimiento (Eje 3)

| Campo | Detalle |
|---|---|
| **Actor(es)** | Estudiante, Unidad Académica |
| **Descripción** | Permite registrar las actividades de transferencia de conocimiento realizadas hacia las comunidades durante el EPS. |
| **Precondiciones** | El estudiante debe tener su expediente activo. |
| **Flujo principal** | 1. El estudiante accede al módulo del Eje 3.<br>2. Registra la actividad de transferencia (capacitación, taller, asesoría, etc.).<br>3. Indica la comunidad o grupo beneficiado y la fecha.<br>4. El sistema guarda el registro.<br>5. La unidad académica consulta las actividades registradas. |
| **Flujos alternativos** | 2a. Actividad realizada en conjunto con otros estudiantes → se registra individualmente por cada participante. |
| **Postcondiciones** | La actividad de transferencia queda registrada y disponible para consolidación institucional. |

---

### CU-08 · Registro de Territorio y Geolocalización (Eje 4)

| Campo | Detalle |
|---|---|
| **Actor(es)** | Estudiante, Unidad Académica |
| **Descripción** | Permite capturar la ubicación geográfica y el contexto territorial donde se desarrolla el EPS. |
| **Precondiciones** | El estudiante debe tener su expediente activo. |
| **Flujo principal** | 1. El estudiante accede al módulo del Eje 4.<br>2. Ingresa o selecciona en el mapa la ubicación donde se desarrolla el EPS.<br>3. Complementa con datos de contexto territorial (departamento, municipio, comunidad).<br>4. El sistema guarda el registro geolocalizado.<br>5. DIGEU y la unidad académica pueden visualizar la distribución territorial del EPS. |
| **Flujos alternativos** | 2a. Ubicación exacta no disponible → el estudiante registra la referencia territorial más cercana (municipio/comunidad). |
| **Postcondiciones** | El contexto territorial queda registrado y disponible para reportes de cobertura geográfica. |

---

### CU-09 · Registro de Actores y Participantes (Eje 5)

| Campo | Detalle |
|---|---|
| **Actor(es)** | Estudiante, Unidad Académica |
| **Descripción** | Permite registrar a los actores involucrados en el EPS: la institución receptora, la contraparte, la comunidad y las instituciones aliadas que participaron o cooperaron con el proyecto. |
| **Precondiciones** | El estudiante debe tener su expediente activo. |
| **Flujo principal** | 1. El estudiante accede al módulo del Eje 5.<br>2. Escribe la institución receptora (donde realiza su EPS), su contraparte y la comunidad beneficiada.<br>3. Agrega las instituciones aliadas eligiéndolas del catálogo de DIGEU y describe su aporte.<br>4. El sistema guarda el registro de actores y de alianzas.<br>5. La unidad académica consulta los actores vinculados al EPS de sus estudiantes. |
| **Flujos alternativos** | 2a. La institución aliada no está en el catálogo → el estudiante no puede crearla; DIGEU la agrega al catálogo y el estudiante la elige. |
| **Postcondiciones** | Los actores quedan registrados y vinculados al expediente del estudiante. |

---

### CU-10 · Registro de Seguimiento e Impacto (Eje 6)

| Campo | Detalle |
|---|---|
| **Actor(es)** | Estudiante, Unidad Académica |
| **Descripción** | Permite registrar indicadores de seguimiento y evaluar el impacto del ejercicio profesional supervisado. |
| **Precondiciones** | El estudiante debe tener registros previos en al menos uno de los otros ejes. |
| **Flujo principal** | 1. El estudiante o la unidad académica accede al módulo del Eje 6.<br>2. Registra los indicadores de seguimiento definidos (avance, cumplimiento, observaciones).<br>3. Registra la evaluación de impacto al finalizar el EPS.<br>4. El sistema guarda el registro.<br>5. DIGEU consulta los indicadores consolidados. |
| **Flujos alternativos** | 2a. Seguimiento periódico → el registro se repite en distintos momentos del EPS (avances parciales). |
| **Postcondiciones** | Los indicadores de seguimiento e impacto quedan registrados y disponibles para reportería. |

---

## Mes 3 — Reportería, auditoría y pruebas

### CU-11 · Consolidación y Reportería (DIGEU)

| Campo | Detalle |
|---|---|
| **Actor(es)** | DIGEU |
| **Descripción** | Permite a DIGEU consolidar la información capturada en todas las unidades académicas, filtrarla por unidad y eje, y exportar reportes. |
| **Precondiciones** | Deben existir registros capturados en al menos uno de los seis ejes. |
| **Flujo principal** | 1. DIGEU accede al panel de consolidación.<br>2. Selecciona los filtros deseados (unidad académica, eje, rango de fechas).<br>3. El sistema consolida y muestra la información filtrada.<br>4. DIGEU exporta el reporte en el formato disponible (Excel/PDF). |
| **Flujos alternativos** | 3a. No hay registros que coincidan con los filtros → el sistema muestra un mensaje indicando que no hay resultados. |
| **Postcondiciones** | DIGEU obtiene un reporte consolidado de la información institucional del EPS. |

---

### CU-12 · Auditoría y Bitácora de cambios

| Campo | Detalle |
|---|---|
| **Actor(es)** | DIGEU, Sistema |
| **Descripción** | Registra automáticamente los cambios realizados en el sistema, permitiendo trazabilidad de capturas y ediciones por usuario. |
| **Precondiciones** | Debe existir al menos una acción de creación, edición o eliminación en el sistema. |
| **Flujo principal** | 1. Un usuario realiza una acción de creación, edición o eliminación sobre un registro.<br>2. El sistema registra automáticamente la acción en la bitácora (usuario, fecha/hora, módulo, tipo de cambio).<br>3. DIGEU accede al módulo de auditoría.<br>4. DIGEU consulta o filtra la bitácora por usuario, módulo o fecha. |
| **Flujos alternativos** | 4a. DIGEU no encuentra registros con los filtros aplicados → el sistema muestra el listado vacío. |
| **Postcondiciones** | Toda acción relevante queda trazada y disponible para consulta de auditoría. |

---

### CU-13 · Ejecución de Pruebas y Documentación Técnica

| Campo | Detalle |
|---|---|
| **Actor(es)** | Equipo de desarrollo / QA |
| **Descripción** | Permite ejecutar pruebas de control de calidad sobre los módulos desarrollados, corregir observaciones y generar la documentación técnica de entrega. |
| **Precondiciones** | Los 12 módulos funcionales deben estar desarrollados. |
| **Flujo principal** | 1. El equipo de desarrollo ejecuta pruebas funcionales sobre cada módulo.<br>2. Registra las observaciones o errores encontrados.<br>3. Corrige las observaciones identificadas.<br>4. Elabora la documentación técnica del sistema (instalación, estructura, módulos).<br>5. Entrega el sistema junto con la documentación técnica. |
| **Flujos alternativos** | 2a. Se detectan errores críticos → se prioriza su corrección antes de continuar con las pruebas restantes. |
| **Postcondiciones** | El sistema queda validado, con sus observaciones corregidas y documentación técnica lista para entrega. |

---

*Documento generado como parte de la propuesta técnica del sistema SIRSE-EPS / USAC.*

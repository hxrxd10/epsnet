# SIRSE USAC: cómo quedó el sistema

**Nombre completo:** Sistema Unificado de Registro y Seguimiento del EPS de la Universidad de San Carlos de Guatemala.
**Unidad responsable:** Dirección General de Extensión Universitaria (DIGEU).
**Nombres anteriores:** EPSNET y SIRSE-EPS. Algunos correos de prueba y el repositorio aún usan `epsnet`.
**Corte de esta descripción:** 31 de octubre de 2026. Incluye las correcciones posteriores al commit `80b34012` de `main`.

## 1. Qué hace el sistema

SIRSE USAC centraliza lo que los estudiantes realizan durante su Ejercicio Profesional Supervisado (EPS). El estudiante documenta su EPS en seis ejes, la supervisión de su unidad académica lo revisa y lo aprueba, y solo entonces la información cuenta en estadísticas, repositorio e investigaciones.

El sistema unifica el formato y consolida los datos en DIGEU sin interferir con los procesos internos de cada unidad. También apoya a la USAC en el proyecto FUESE (Erasmus+), porque ayuda a documentar, comparar y cuantificar la extensión universitaria.

## 2. Flujo general

1. **Identidad:** el estudiante verifica su registro académico y DPI contra el servicio de Registro y Estadística. El DPI no se almacena.
2. **Carrera:** elige la carrera en la que realiza el EPS. Cada carrera tiene un expediente, y la unidad académica se obtiene de la carrera.
3. **Registro:** llena los seis ejes y puede indicar si el EPS pertenece al EPSUM.
4. **Cierre:** revisa el resumen, sube su mejor foto y, si la tiene, su orden de impresión.
5. **Envío:** manda el EPS a aprobación. Debe tener al menos un registro en los ejes.
6. **Revisión:** el EPS llega a la bandeja de su unidad y de DIGEU, en orden de llegada.
7. **Aprobación:** la supervisión lo aprueba marcando un acepto de que lo descrito está comprobado y se ejecutó.
8. **Consolidación:** el EPS aparece en repositorio e investigaciones y cuenta en estadísticas y ODS.

### Estados de un EPS

| Estado en pantalla | Valor técnico | Significado | ¿Cuenta en consultas? |
|---|---|---|---|
| En progreso | `activo` | El estudiante aún lo está llenando | No |
| En revisión | `completo` | Enviado, en la bandeja de supervisión | No |
| Aprobado | `verificado` | Aprobado por supervisión | Sí |

- La orden de impresión es opcional y, por sí sola, no aprueba un EPS.
- Un EPS **Aprobado** es de solo lectura: el estudiante puede ver lo que consignó, pero no cambiar registros, programa, foto ni orden de impresión.
- Si hay que corregirlo, la supervisión retira la aprobación: el EPS vuelve a **En revisión** y el estudiante puede editarlo de nuevo.

## 3. Roles y permisos

| Función | DIGEU | Unidad académica | Estudiante | Invitado |
|---|---|---|---|---|
| Panel y documentación | Sí | Sí | Sí | Sí |
| Llenar su propio EPS | No | No | Sí | No |
| Estadísticas, ODS, repositorio e investigaciones | Todo | Solo su alcance | No | No |
| Exportar PDF y Excel | Todo | Solo su alcance | No | No |
| Bandeja de solicitudes | Todas | Solo su alcance | No | No |
| Estudiantes y EPS (revisar y aprobar) | Todos | Solo su alcance | No | No |
| Usuarios, manejo de datos y bitácora | Sí | No | No | No |

**Alcance de una unidad académica:** ve los EPS de su unidad. Si DIGEU le asigna carreras, ve únicamente esas carreras. Una unidad puede tener varias cuentas; por ejemplo, un supervisor por carrera.

**Cómo se obtiene cada rol:**

- **Invitado:** quien se registra en `/registro`. Por ahora no tiene acceso a las consultas.
- **Estudiante:** quien verifica registro académico y DPI. Un invitado también puede convertirse en estudiante con esa verificación. Solo ve su propio EPS; no entra a las consultas.
- **Unidad académica y DIGEU:** cuentas creadas o asignadas por DIGEU.

## 4. Los seis ejes del EPS

| Eje | Qué se registra | Función |
|---|---|---|
| 1. Bienes y servicios | Bien o servicio del catálogo, cantidad, beneficiarios, poblaciones vulnerables y fecha | Mide el aporte y a quién llega |
| 2. Publicaciones de investigación | Título, tipo, autores, medio, resumen, enlace y fecha | Alimenta el módulo Investigaciones |
| 3. Transferencia de conocimiento | Capacitaciones, talleres, asesorías, documentos, comunidad y participantes | Mide las acciones formativas |
| 4. Territorio | Departamento, municipio, comunidad, punto en el mapa y referencia | Ubica el EPS en el mapa |
| 5. Actores y participantes | Institución receptora, contraparte, comunidad e instituciones aliadas | Mide la vinculación |
| 6. Seguimiento e impacto | Avances parciales y evaluación final de impacto | Documenta avance y resultados |

## 5. Módulos del sistema

| Módulo | Contenido |
|---|---|
| Sitio público | Presentación del sistema, ejes y contacto. No muestra estadísticas públicas |
| Autenticación | Registro, inicio de sesión, verificación de correo, recuperación de contraseña, doble factor y confirmación de contraseña |
| Mis carreras y EPS | Carreras del estudiante y asistente de registro por pasos |
| Bandeja de solicitudes | EPS pendientes en orden de llegada, con turno y días de espera |
| Estudiantes y EPS | Búsqueda, filtros, revisión completa, orden de impresión, aprobación y retiro |
| Estadísticas | Mapa, nueve métricas, filtros por año, fechas, unidad, carrera y eje, detalle por departamento y municipio, PDF y Excel |
| ODS | Aporte a cada Objetivo de Desarrollo Sostenible y exportación a Excel |
| Repositorio | EPS aprobados, foto, descripción, articulación y filtros |
| Investigaciones | Publicaciones de EPS aprobados, con búsqueda y filtros |
| Usuarios | Crear cuentas, cambiar acceso, asignar unidad y carreras, y corregir correos |
| Manejo de datos | Catálogos, territorio, instituciones aliadas, carreras y unidades |
| Bitácora | Trazabilidad de cambios, accesos, aprobaciones y exportaciones |
| Documentación | Guía de uso dentro del sistema, según el rol |
| Ajustes | Perfil, seguridad, apariencia y eliminación de la cuenta |

## 6. Indicadores

### Métricas de estadísticas

Solo cuentan los EPS aprobados.

| Métrica | Cálculo |
|---|---|
| EPS | Cantidad de expedientes aprobados |
| Estudiantes | Estudiantes distintos |
| Bienes y servicios | Suma de cantidades. Un registro sin cantidad cuenta como 1 |
| Aporte (Q) | Cantidad de cada bien o servicio por su valor base del catálogo |
| Beneficiarios | Suma de beneficiarios indicados en los bienes y servicios |
| Acciones de transferencia | Cantidad de actividades registradas |
| Participantes | Suma de participantes de las actividades |
| Investigaciones | Cantidad de publicaciones |
| Instituciones aliadas | Instituciones distintas |

**Reglas de interpretación:**

- **Año y rango de fechas:** se usa la fecha de la orden de impresión. Si no existe, la de la aprobación.
- **Eje:** al elegirlo, solo cuentan los EPS con registros en ese eje. En Actores y participantes también cuentan las instituciones aliadas.
- **Ubicación:** todo el EPS se atribuye a su primera ubicación registrada. Si no tiene ubicación, cuenta en los totales, pero no en un departamento.
- **Sin catálogo:** un bien sin elemento de catálogo se muestra como sin clasificar y aporta Q0.
- **Participantes y beneficiarios:** son conteos distintos y no deben sumarse.
- **Aporte:** es una estimación basada en valores promedio, no un dato contable.

### Indicador de articulación

Indica cuántas funciones universitarias combina un EPS además de la docencia. Va de 0 a 4 y se calcula de forma automática.

| Función | Se demuestra con |
|---|---|
| Extensión | Al menos un bien o servicio |
| Investigación | Al menos una publicación |
| Transferencia de conocimiento | Al menos una acción de transferencia |
| Vinculación con aliadas | Al menos una institución aliada |

Niveles: **0** Sin articulación, **1** Inicial, **2** Básica, **3** Articulada y **4** Integral.

### Vínculo de los bienes y servicios

Cada elemento del catálogo se ancla a una línea de intervención, una política pública, un área de intervención y un ODS, y puede tener valor base en quetzales. El catálogo inicial tiene 239 bienes y servicios con valores promedio, los 17 ODS y 10 poblaciones vulnerables.

## 7. Exportaciones

| Pantalla | PDF | Excel | Contenido del Excel |
|---|---|---|---|
| Estadísticas del país | Sí | Sí | Resumen, departamentos, EPS, articulación, funciones y poblaciones vulnerables |
| Departamento | Sí | Sí | Resumen, municipios, bienes y servicios, investigaciones, aliadas y EPS |
| ODS | No | Sí | ODS, bienes y servicios, líneas, áreas y unidades |

Las exportaciones respetan los filtros y el alcance del usuario, y quedan anotadas en la bitácora.

## Correo y contraseña

- Al crear una cuenta, el sistema envía el enlace de verificación al correo indicado.
- Si alguien se equivocó de correo, DIGEU lo corrige en **Usuarios > Cambiar correo**, incluso en cuentas de estudiante. La verificación se envía a la nueva dirección.
- Si la persona olvidó su contraseña, la restablece con el correo correcto en **¿Olvidaste tu contraseña?**.

## 8. Privacidad y control

- El DPI se usa solo para comparar la identidad y no se almacena.
- El repositorio no muestra carné ni datos de contacto de instituciones.
- Una consulta de la unidad académica nunca incluye EPS fuera de su alcance.
- La bitácora guarda valores anteriores y nuevos, excluye contraseñas y secretos, y no se puede editar ni eliminar desde el sistema.
- Las fotos y órdenes de impresión se guardan en base64 dentro de la base de datos.

## 9. Limitaciones conocidas

1. **Edición durante la revisión.** Mientras un EPS está En revisión, el estudiante aún puede corregirlo, sin aviso a la supervisión. Una vez aprobado ya no puede cambiar nada.
2. **Supervisión con solo lectura.** La unidad académica no registra seguimiento desde su panel; lo hace el estudiante.
3. **Doble factor opcional.** Está disponible, pero no es obligatorio para DIGEU.
4. **Dependencia de servicios externos.** Sin el servicio de Registro y Estadística no se pueden verificar estudiantes nuevos ni consultar carreras. Sin Google Maps, el sistema usa un mapa alterno de puntos.
5. **Crecimiento de la base de datos.** Los archivos en base64 aumentan el tamaño de la base y de sus respaldos.
6. **Textos desactualizados.** La descripción del rol invitado aún dice que consulta estadísticas, y la tarjeta de Usuarios del panel no menciona la creación de cuentas.
7. **Sin acceso público a resultados.** FUESE plantea cuantificar logros también hacia el exterior, pero las consultas requieren una cuenta y el invitado no tiene acceso por ahora.

### Decisiones de alcance confirmadas

- **Formularios de ingreso:** los seis formularios corresponden a uno por eje, así que el asistente de seis ejes los cubre.
- **Evidencias y datos del EPSUM:** el EPSUM ya conserva las evidencias y el detalle de sus estudiantes. En SIRSE USAC solo se marca que el EPS pertenece al programa.

## 10. Fase 2 acordada

- Portal público de resultados.
- Medición de impacto con línea base.
- Módulo de buenas prácticas.

## 11. Glosario

| Término | Definición |
|---|---|
| EPS | Ejercicio Profesional Supervisado |
| EPSUM | Programa de EPS Multiprofesional. El documento de FUESE lo nombra Multidisciplinario; conviene unificar el término con DIGEU |
| DIGEU | Dirección General de Extensión Universitaria |
| FUESE | Fortalecimiento de la Extensión en el Ecosistema Social de las Universidades en Centroamérica |
| ODS | Objetivo de Desarrollo Sostenible |
| Unidad académica | Facultad, escuela o centro universitario |
| Expediente | Registro del EPS de un estudiante en una carrera |
| Orden de impresión | Documento opcional adjunto al EPS |
| Institución receptora | Donde se realiza el EPS. Se escribe libremente y no se cuantifica |
| Institución aliada | Institución que cooperó. Se elige del catálogo y sí se cuantifica |
| Beneficiarios | Personas alcanzadas por un bien o servicio |
| Participantes | Personas que participaron en una acción de transferencia |
| Aporte | Estimación en quetzales: cantidad por valor base |

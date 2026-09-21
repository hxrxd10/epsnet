# Documento de Especificaciones, Requerimientos y Criterios (DERC)

**Sistema Unificado de Registro y Seguimiento del Ejercicio Profesional Supervisado**
*(SIRSE-EPS / USAC)*

## 1. Introducción

El presente documento formaliza las especificaciones funcionales, los requerimientos no funcionales y los criterios de aceptación del sistema SIRSE-EPS, a partir de la arquitectura institucional de seis ejes, la estructura de acceso en tres niveles (estudiante, unidad académica, DIGEU) y los 13 módulos definidos en la propuesta técnica y económica del proyecto.

## 2. Objetivo del documento

Establecer de forma clara y verificable qué debe hacer cada módulo del sistema (requerimientos funcionales) y bajo qué condiciones se considera correctamente implementado (criterios de aceptación), sirviendo como base para el desarrollo, las pruebas y la validación de entrega de cada mes del proyecto.

## 3. Alcance

Este documento cubre los 13 módulos funcionales a desarrollar en Laravel, organizados en los tres entregables mensuales acordados. No cubre aspectos de infraestructura, hosting, despliegue en producción, capacitación a usuarios finales ni soporte posterior a la entrega, ya que estos elementos están fuera del alcance contractual del desarrollo.

## 4. Arquitectura general

- Estructura de acceso en 3 niveles: Estudiante, Unidad Académica y DIGEU (administrador central).
- Arquitectura de datos de 6 ejes: Bienes y Servicios Generados, Publicaciones de Investigación, Transferencia de Conocimiento, Territorio/Geolocalización, Actores/Participantes, y Seguimiento e Impacto.
- Principio de diseño: unificación de formato y consolidación de datos a nivel central (DIGEU), sin interferir con los procesos internos de cada unidad académica.

## 5. Requerimientos funcionales y criterios de aceptación por módulo

### Mes 1 — Parte administrativa

#### Módulo 1 — Autenticación y Roles (Mes 1)

**Requerimientos funcionales**
- RF-01: El sistema debe permitir el inicio de sesión mediante usuario y contraseña, validando las credenciales contra la base de datos.
- RF-02: El sistema debe identificar el rol del usuario autenticado (estudiante, unidad académica, DIGEU) y restringir el acceso a los módulos correspondientes mediante middleware de permisos.

**Criterios de aceptación**
- Un usuario con credenciales válidas accede únicamente a las funciones de su rol.
- Un usuario con credenciales inválidas recibe un mensaje de error y puede reintentar el ingreso.
- Un usuario sin rol asignado no puede acceder a ningún módulo del sistema.

#### Módulo 2 — Unidades Académicas (Mes 1)

**Requerimientos funcionales**
- RF-03: El sistema debe permitir a DIGEU crear, consultar, actualizar y eliminar unidades académicas (facultad, escuela o centro universitario).
- RF-04: El sistema debe permitir asignar un usuario administrador a cada unidad académica.

**Criterios de aceptación**
- DIGEU puede registrar una nueva unidad académica con nombre, tipo y datos de contacto.
- El sistema valida que los campos obligatorios estén completos antes de guardar el registro.
- El usuario administrador asignado a una unidad accede al panel correspondiente a esa unidad.

#### Módulo 3 — Gestión de Estudiantes/EPS (Mes 1)

**Requerimientos funcionales**
- RF-05: El sistema debe permitir a la unidad académica registrar estudiantes y asignarlos a un programa (EPS facultativo o EPSUM).
- RF-06: El sistema debe generar un expediente único por estudiante, vinculado a su perfil y unidad académica.

**Criterios de aceptación**
- El sistema evita el registro de un estudiante duplicado (mismo carnet o usuario).
- Al asignar el programa EPSUM se habilitan los campos específicos del programa multiprofesional.
- El estudiante registrado puede acceder a su perfil y expediente tras la creación de su cuenta.

#### Módulo 4 — Formularios de Ingreso (6) (Mes 1)

**Requerimientos funcionales**
- RF-07: El sistema debe permitir a los estudiantes completar los seis formularios de ingreso definidos institucionalmente, con validación de campos obligatorios.
- RF-08: El sistema debe permitir el guardado parcial del avance de un formulario.
- RF-09: El sistema debe permitir adjuntar documentos de respaldo a cada formulario.

**Criterios de aceptación**
- El estudiante puede guardar un formulario incompleto y retomarlo posteriormente sin perder el avance.
- El sistema bloquea el envío final si existen campos obligatorios vacíos o con formato inválido.
- Los formularios enviados quedan disponibles para consulta de la unidad académica y de DIGEU.

### Mes 2 — Los seis ejes

#### Módulo 5 — Eje 1 — Bienes y Servicios Generados (Mes 2)

**Requerimientos funcionales**
- RF-10: El sistema debe permitir al estudiante registrar los bienes y servicios generados durante su EPS (tipo, descripción, beneficiarios, fecha).
- RF-11: El sistema debe permitir adjuntar evidencia al registro cuando corresponda.

**Criterios de aceptación**
- El estudiante puede registrar múltiples bienes o servicios de forma independiente.
- Los registros quedan vinculados al expediente del estudiante y visibles para su unidad académica.

#### Módulo 6 — Eje 2 — Publicaciones de Investigación (Mes 2)

**Requerimientos funcionales**
- RF-12: El sistema debe permitir registrar investigaciones y publicaciones vinculadas al ejercicio profesional (título, tipo, autores, medio de publicación).
- RF-13: El sistema debe permitir adjuntar el documento o enlace correspondiente a la publicación.

**Criterios de aceptación**
- El registro puede completarse sin adjunto y complementarse posteriormente.
- La unidad académica puede consultar y validar las publicaciones registradas por sus estudiantes.

#### Módulo 7 — Eje 3 — Transferencia de Conocimiento (Mes 2)

**Requerimientos funcionales**
- RF-14: El sistema debe permitir registrar actividades de transferencia de conocimiento hacia comunidades (capacitación, taller, asesoría).
- RF-15: El sistema debe registrar la comunidad o grupo beneficiado y la fecha de la actividad.

**Criterios de aceptación**
- Una actividad realizada por varios estudiantes se registra de forma individual por cada participante.
- Las actividades registradas son consultables por la unidad académica correspondiente.

#### Módulo 8 — Eje 4 — Territorio/Geolocalización (Mes 2)

**Requerimientos funcionales**
- RF-16: El sistema debe permitir capturar la ubicación geográfica donde se desarrolla el EPS, mediante selección en mapa o ingreso manual.
- RF-17: El sistema debe registrar el contexto territorial (departamento, municipio, comunidad). El departamento y el municipio se eligen de los catálogos administrados por DIGEU, cada uno con una coordenada de referencia; las unidades académicas registran además las coordenadas de su edificio.

**Criterios de aceptación**
- Cuando no se dispone de ubicación exacta, el sistema permite registrar la referencia territorial más cercana.
- DIGEU y la unidad académica pueden visualizar la distribución territorial de los registros del EPS.

#### Módulo 9 — Eje 5 — Actores/Participantes (Mes 2)

**Requerimientos funcionales**
- RF-18: El sistema debe permitir registrar la institución receptora, la contraparte y la comunidad beneficiada del EPS.
- RF-19: El sistema debe permitir reutilizar una institución receptora ya registrada por otro estudiante.

**Criterios de aceptación**
- El sistema no permite instituciones receptoras duplicadas con el mismo nombre.
- Los actores registrados quedan vinculados al expediente del estudiante correspondiente.

#### Módulo 10 — Eje 6 — Seguimiento e Impacto (Mes 2)

**Requerimientos funcionales**
- RF-20: El sistema debe permitir registrar indicadores de seguimiento (avance, cumplimiento, observaciones) durante el EPS.
- RF-21: El sistema debe permitir registrar la evaluación de impacto al finalizar el ejercicio.

**Criterios de aceptación**
- El registro de seguimiento puede repetirse en distintos momentos del EPS (avances parciales).
- DIGEU puede consultar los indicadores consolidados por estudiante y por unidad académica.

### Mes 3 — Reportería, auditoría y pruebas

#### Módulo 11 — Consolidación y Reportería (DIGEU) (Mes 3)

**Requerimientos funcionales**
- RF-22: El sistema debe permitir a DIGEU consolidar la información capturada en todas las unidades académicas.
- RF-23: El sistema debe permitir filtrar la información por unidad académica, eje y rango de fechas.
- RF-24: El sistema debe permitir exportar los reportes en formato Excel y/o PDF.

**Criterios de aceptación**
- Los filtros aplicados devuelven únicamente los registros que cumplen los criterios seleccionados.
- Cuando no existen registros que coincidan con los filtros, el sistema muestra un mensaje indicándolo.
- El archivo exportado refleja exactamente los datos mostrados en el panel de consolidación.

#### Módulo 12 — Auditoría / Bitácora (Mes 3)

**Requerimientos funcionales**
- RF-25: El sistema debe registrar automáticamente toda acción de creación, edición o eliminación (usuario, fecha/hora, módulo, tipo de cambio).
- RF-26: El sistema debe permitir a DIGEU consultar y filtrar la bitácora por usuario, módulo o fecha.

**Criterios de aceptación**
- Toda acción relevante sobre un registro queda trazada sin intervención manual del usuario.
- Los registros de bitácora no pueden ser editados ni eliminados por ningún usuario.

#### Módulo 13 — Pruebas y Documentación Técnica (Mes 3)

**Requerimientos funcionales**
- RF-27: El equipo de desarrollo debe ejecutar pruebas funcionales sobre cada uno de los 12 módulos previos.
- RF-28: El equipo de desarrollo debe elaborar documentación técnica de instalación, estructura y módulos del sistema.

**Criterios de aceptación**
- Las observaciones o errores detectados quedan registrados y corregidos antes de la entrega final.
- Los errores críticos se priorizan y resuelven antes de continuar con las pruebas restantes.
- El sistema se entrega junto con la documentación técnica completa.

## 6. Requerimientos no funcionales

| Categoría | Requerimiento |
| --- | --- |
| Seguridad | Las contraseñas deben almacenarse cifradas (hash). El acceso a cada módulo debe validarse mediante control basado en roles (RBAC). Las entradas de formularios deben validarse en servidor. |
| Rendimiento | Las operaciones de registro y consulta sobre los módulos deben responder en tiempos razonables bajo uso normal (sin picos de carga masiva). |
| Usabilidad | La interfaz debe ser responsive y los mensajes de validación de formularios deben ser claros y específicos por campo. |
| Compatibilidad | El sistema debe operar correctamente en navegadores modernos (Chrome, Firefox, Edge) en sus versiones vigentes. |
| Integridad de datos | Las relaciones entre entidades deben mantenerse consistentes conforme al modelo de datos (DER) definido para el proyecto. |
| Mantenibilidad | El código debe seguir la arquitectura MVC de Laravel, con estructura modular que permita dar mantenimiento independiente a cada uno de los 13 módulos. |
| Trazabilidad | Toda acción de creación, edición o eliminación debe quedar registrada en la bitácora del sistema (Módulo 12), sin excepción. |
| Disponibilidad | No aplica un SLA de disponibilidad, dado que el hosting y el despliegue en producción están fuera del alcance de este entregable. |

## 7. Supuestos y restricciones

- El entregable de cada mes está condicionado al pago correspondiente al mes anterior, según el cronograma de la propuesta económica.
- El entregable de este proyecto es exclusivamente el código fuente de los 13 módulos; no incluye hosting, dominio, despliegue en producción, capacitación ni soporte posterior.
- Se asume que la información de catálogo institucional (facultades, escuelas, centros universitarios) será proporcionada por DIGEU durante la etapa de configuración inicial.
- Cualquier requerimiento adicional fuera del alcance de los 13 módulos aquí descritos se documentará y cotizará por separado.

## 8. Glosario

- **EPS**: Ejercicio Profesional Supervisado.
- **EPSUM**: Programa de Ejercicio Profesional Supervisado Multiprofesional.
- **DIGEU**: Dirección General de Extensión Universitaria.
- **Unidad Académica**: Facultad, escuela o centro universitario de la USAC.
- **Eje**: Cada una de las seis dimensiones de datos definidas en la arquitectura del sistema (bienes/servicios, publicaciones, transferencia de conocimiento, territorio, actores, seguimiento e impacto).
- **Bitácora**: Registro automático de cambios (creación, edición, eliminación) realizados por los usuarios en el sistema.

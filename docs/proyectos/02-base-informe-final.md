# Base para el informe final de SIRSE USAC

Usa este archivo junto con `01-como-quedo-el-sistema.md`. Los datos que no aparecen aquí deben quedar como `[PENDIENTE]`.

## 1. Estructura sugerida

1. Portada y datos del proyecto.
2. Resumen ejecutivo.
3. Antecedentes y justificación.
4. Objetivos.
5. Alcance.
6. Metodología y desarrollo por etapas.
7. Resultados: el sistema entregado.
8. Cumplimiento de requerimientos.
9. Cambios frente a lo planificado.
10. Alineación con FUESE.
11. Calidad y pruebas.
12. Limitaciones y riesgos.
13. Conclusiones y recomendaciones.
14. Próximos pasos.
15. Anexos: casos de uso, requerimientos, manuales y capturas.

## 2. Datos del proyecto

| Dato | Valor |
|---|---|
| Sistema | SIRSE USAC |
| Institución | Universidad de San Carlos de Guatemala |
| Unidad responsable | DIGEU |
| Marco de cooperación | FUESE, cofinanciado por la Comisión Europea mediante Erasmus+ |
| Contrato, fechas, montos y responsables | [PENDIENTE] |
| Primer commit en el repositorio | 20 de septiembre de 2026 |
| Último commit descrito | 24 de octubre de 2026, `80b34012` |

Las fechas de los commits muestran la actividad registrada en el repositorio, no necesariamente las fechas contractuales.

## 3. Alcance contractual

Según `derc.md`:

- 13 módulos repartidos en tres entregas mensuales: administración, seis ejes, y reportería, auditoría y pruebas.
- Tres niveles de acceso y seis ejes de datos.
- El entregable es el código fuente.
- No incluye hosting, dominio, despliegue en producción, capacitación ni soporte posterior.
- Lo adicional se documenta y cotiza por separado.

El detalle económico y el cronograma están en `propuesta-economica.md`, que no está en el repositorio: [PENDIENTE].

Se agregaron funciones fuera de los 13 módulos y el sistema fue desplegado en Hostinger. Si esto se trató como adicional, su respaldo contractual queda como [PENDIENTE].

## 4. Hitos según el repositorio

| Fecha | Avance |
|---|---|
| 20/09/2026 | Base del proyecto, portada, asistente del estudiante, roles, usuarios, repositorio y conexión con Registro y Estadística |
| 20/09/2026 | Mapa estadístico, catálogos de territorio y unidades, bitácora, PDF, acepto de aprobación y EPSUM |
| 21/09/2026 | Validaciones de unidades académicas y preparación del despliegue por `git pull` |
| 24/10/2026 | Cambio de nombre, aprobación obligatoria, supervisores por carrera, foto, aporte e investigaciones |
| 24/10/2026 | Mapa sobre Google Maps y pestaña ODS |
| 24/10/2026 | Poblaciones vulnerables, indicador de articulación y exportación a Excel |
| 24/10/2026 | Consultas limitadas por unidad y carreras, y cierre del acceso de invitados |
| 31/10/2026 | EPS aprobado de solo lectura, corrección de correos por DIGEU y filtros por eje y rango de fechas |

## 5. Matriz de cumplimiento

**Clave:** *Cumple* = disponible como se pidió; *Modificado* = resuelto de otra forma; *Parcial* = falta una parte; *Sustituido* = reemplazado por otro diseño.

| RF | Requerimiento | Estado | Cómo quedó |
|---|---|---|---|
| RF-01 | Inicio de sesión | Cumple | Correo y contraseña, con doble factor opcional |
| RF-02 | Acceso por rol | Cumple | Cuatro roles y control por ruta. Se agregó Invitado |
| RF-03 | CRUD de unidades | Cumple | Con uso activo, se desactivan en lugar de eliminarse |
| RF-04 | Administrador por unidad | Modificado | Varias cuentas por unidad, con carreras opcionales |
| RF-05 | La unidad registra estudiantes | Modificado | El estudiante se verifica contra Registro y Estadística y elige carrera y programa |
| RF-06 | Expediente único por estudiante | Modificado | Un expediente por estudiante y carrera |
| RF-07 | Seis formularios de ingreso | Cumple | Un formulario por eje, en un asistente con validación en servidor |
| RF-08 | Guardado parcial | Cumple | Cada registro se guarda al instante y se retoma donde quedó |
| RF-09 | Adjuntos por formulario | Modificado | Foto y orden de impresión por EPS. Las demás evidencias las conserva el EPSUM |
| RF-10 | Registrar bienes y servicios | Cumple | Agrega cantidad, catálogo, valor y poblaciones vulnerables |
| RF-11 | Evidencia por registro | Modificado | Las evidencias las conserva el EPSUM; SIRSE USAC solo marca el programa |
| RF-12 | Registrar publicaciones | Cumple | Con módulo propio de Investigaciones |
| RF-13 | Documento o enlace | Cumple | Por enlace; no se adjunta archivo |
| RF-14 | Actividades de transferencia | Cumple | Incluye documento o material generado |
| RF-15 | Comunidad y fecha | Cumple | Validadas en servidor |
| RF-16 | Ubicación en mapa o manual | Cumple | Punto opcional que sugiere departamento y municipio |
| RF-17 | Contexto territorial | Cumple | Catálogos de 22 departamentos y 340 municipios |
| RF-18 | Receptora, contraparte y comunidad | Cumple | Receptora de texto libre |
| RF-19 | Instituciones aliadas | Cumple | Catálogo de DIGEU sin duplicados |
| RF-20 | Indicadores de seguimiento | Cumple | Avance, porcentaje, cumplimiento y observaciones |
| RF-21 | Evaluación de impacto | Cumple | Obligatoria en un registro de evaluación final |
| RF-22 | Consolidación | Cumple | Estadísticas, ODS, repositorio e investigaciones |
| RF-23 | Filtros por unidad, eje y fechas | Cumple | Unidad, eje, año, rango de fechas y carrera, también en PDF, Excel y ODS |
| RF-24 | Exportar a Excel o PDF | Cumple | PDF de estadísticas y Excel de estadísticas y ODS |
| RF-25 | Registro automático de cambios | Cumple | Aplicado a 17 modelos, más accesos, aprobaciones y exportaciones |
| RF-26 | Consultar bitácora | Cumple | Filtros por texto, módulo, tipo y fechas |
| RF-27 | Pruebas funcionales | Cumple | Suite automatizada; ver sección 7 |
| RF-28 | Documentación técnica | En curso | Se completa con el manual técnico |

**Criterios de aceptación con observación:**

- *Campos específicos de EPSUM:* no se implementaron por decisión del proyecto; el EPSUM ya conserva esos datos y aquí solo se marca el programa.
- *Seguimiento por la unidad (CU-10):* la unidad consulta, pero no registra.
- *Exclusión de estudiantes duplicados:* el carné es único y se actualiza el perfil existente.

## 6. Funciones agregadas fuera de los 13 módulos

| Función | Valor para DIGEU |
|---|---|
| Verificación contra Registro y Estadística | Evita registrar datos manualmente y confirma identidad y carreras |
| Bandeja de aprobación en orden de llegada | Ordena la supervisión y muestra el tiempo de espera |
| Aprobación obligatoria con acepto | Solo lo aprobado cuenta en resultados |
| Supervisores por carrera | Permite delegar en unidades grandes |
| Portal de carreras por carné | Agrega carreras desde el registro oficial |
| Valor base y aporte en quetzales | Da una estimación económica de lo entregado |
| Anclaje a línea, política, área y ODS | Conecta el EPS con prioridades públicas |
| Pestaña ODS | Muestra la contribución a cada objetivo |
| Poblaciones vulnerables | Identifica a qué grupos se atiende |
| Indicador de articulación | Mide la integración de funciones universitarias |
| Repositorio e investigaciones | Hace consultable lo aprobado |
| Mapa de Google con límites oficiales | Mejora la lectura territorial |
| Exportación a Excel | Facilita análisis externos |

## 7. Calidad y pruebas

- **Automatización:** 43 archivos de prueba con Pest.
- **Último resultado completo:** 673 pruebas aprobadas el 3 de noviembre de 2026, en SQLite en memoria.
- **Para repetirla:** `vendor/` está en modo producción y no incluye el comando de pruebas. Hay que correr antes `composer install`.
- **Cobertura funcional:** autenticación, roles, usuarios, catálogos, territorio, ejes, aprobación, bandeja, estadísticas, PDF, Excel, ODS, repositorio, investigaciones, articulación, alcance por unidad, seeders y servicio de Registro y Estadística.
- **Verificación manual:** se comprobó en navegador la vista limitada de una unidad académica, el rechazo de un invitado en estadísticas, el EPS aprobado de solo lectura, el cambio de correo y los filtros nuevos.
- **Integración continua:** `.github/workflows/tests.yml` ejecuta las revisiones al enviar cambios a `main`. Su resultado no fue revisado aquí.
- **Pruebas con usuarios reales:** [PENDIENTE].

No se debe afirmar que el sistema está libre de errores. La sección 9 de `01-como-quedo-el-sistema.md` describe las limitaciones vigentes.

## 8. Decisiones por confirmar con DIGEU

1. ¿Debe ser obligatorio el doble factor para DIGEU?
2. ¿Qué información se publicará en el portal externo de la fase 2?
3. ¿Quién valida los valores base usados para calcular el aporte?
4. ¿Cuál es el nombre oficial del programa: EPSUM Multiprofesional o Multidisciplinario?

## 9. Alineación con FUESE

| Propósito de FUESE | Aporte de SIRSE USAC | Límite actual |
|---|---|---|
| Documentar y comparar la extensión | Formato único en seis ejes y catálogos comunes | Se limita a los EPS de la USAC |
| Articular extensión, docencia, investigación y transferencia | Indicador de articulación por EPS | Mide presencia de registros, no calidad |
| Atender a grupos vulnerables | Poblaciones vulnerables por bien o servicio y filtro en repositorio | No evalúa el cambio en esos grupos |
| Pasar de contar actividades a evaluar cambios | Evaluación de impacto y seguimiento | No hay línea base ni comparación antes/después |
| Vincular con prioridades de desarrollo | Anclaje a política pública y ODS | Depende de la calidad del catálogo |
| Identificar buenas prácticas | Repositorio con EPS aprobados | No hay módulo ni criterios de buenas prácticas |
| Cuantificar logros interna y externamente | Estadísticas, mapa, PDF y Excel | No hay portal público |
| Sostener los aprendizajes institucionalmente | Administración de catálogos, roles, bitácora y manuales | Requiere responsables, procesos y presupuesto |

Conviene distinguir dos cosas: el sistema da una base para medir y documentar, pero su existencia no demuestra por sí sola una transformación institucional ni un impacto social duradero.

## 10. Riesgos

| Riesgo | Efecto | Mitigación sugerida |
|---|---|---|
| Aporte interpretado como dato contable | Conclusiones financieras incorrectas | Presentarlo como estimación y validar valores |
| Baja adopción o registros incompletos | Estadísticas no representativas | Capacitación y seguimiento institucional |
| Falla del servicio de Registro y Estadística | No se pueden verificar estudiantes | Monitoreo y contacto con el responsable |
| Costo o límite de Google Maps | Mapa alterno o gasto no previsto | Mantener límites y responsable de la llave |
| Crecimiento de archivos en la base | Respaldos más lentos | Monitorear y evaluar almacenamiento externo |
| Sin responsable técnico tras la entrega | Falta de mantenimiento | Definirlo; el soporte está fuera del alcance |
| Datos de demostración en producción | Cifras falsas y cuentas inseguras | No ejecutar ese seeder en producción |

## 11. Recomendaciones

**Antes de la operación formal:**

1. Validar con DIGEU los valores base y el catálogo.
2. Hacer una prueba piloto con una facultad y un centro universitario.
3. Definir respaldos y responsables de hosting, correo, Registro y Estadística y Google Maps.

**Fase 2:**

1. Portal público con datos agregados y sin información personal.
2. Línea base y evaluación antes/después para medir impacto.
3. Módulo de buenas prácticas con criterios definidos junto con la red FUESE.

## 12. Resultados reales

No se incluyen cifras de uso porque los datos locales son de demostración. Completa con datos de producción y su fecha de corte:

| Indicador | Valor | Fecha de corte |
|---|---|---|
| EPS aprobados | [PENDIENTE] | [PENDIENTE] |
| Estudiantes | [PENDIENTE] | [PENDIENTE] |
| Unidades académicas activas | [PENDIENTE] | [PENDIENTE] |
| Aporte estimado (Q) | [PENDIENTE] | [PENDIENTE] |
| Beneficiarios | [PENDIENTE] | [PENDIENTE] |
| Investigaciones | [PENDIENTE] | [PENDIENTE] |
| Departamentos con EPS | [PENDIENTE] | [PENDIENTE] |

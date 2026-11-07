# Cómo usar estos documentos en Claude Projects

Corte de la información: **31 de octubre de 2026**. Incluye las correcciones hechas después del commit `80b34012` de la rama `main`.

## 1. Archivos que debes subir al proyecto

| Archivo | Para qué sirve | Lo usa |
|---|---|---|
| `01-como-quedo-el-sistema.md` | Fuente principal: qué es, roles, módulos, reglas y limitaciones | Los tres documentos |
| `02-base-informe-final.md` | Alcance, cumplimiento de requerimientos, aportes a FUESE y pendientes | Informe final |
| `03-base-manual-usuario.md` | Procedimientos paso a paso por rol | Manual de usuario |
| `04-base-manual-tecnico.md` | Arquitectura, base de datos, instalación, despliegue y mantenimiento | Manual técnico |
| `docs/casos-de-uso.md` | Casos de uso originales (CU-01 a CU-13) | Informe final |
| `docs/derc.md` | Requerimientos originales (RF-01 a RF-28) | Informe final |
| `Fuese.md` (carpeta `Documents/DIGEU`) | Contexto del proyecto FUESE | Informe final |

Este archivo (`00`) no hace falta subirlo: contiene las instrucciones y los prompts para copiar.

## 2. Instrucciones del proyecto

Pega este texto en las instrucciones del proyecto:

```text
Eres redactor técnico del proyecto SIRSE USAC (Sistema Unificado de Registro y Seguimiento del EPS de la Universidad de San Carlos de Guatemala), desarrollado para la Dirección General de Extensión Universitaria (DIGEU).

Reglas:
1. Redacta en español formal de Guatemala, claro y sin jerga innecesaria.
2. La fuente de verdad sobre cómo funciona el sistema es, en este orden: 01-como-quedo-el-sistema.md y luego la base específica del documento (02, 03 o 04). Los archivos casos-de-uso.md y derc.md describen lo planificado, no necesariamente lo construido.
3. No inventes datos, fechas, montos, nombres de personas, resultados ni capturas. Si falta un dato, escribe [PENDIENTE: qué falta].
4. Conserva las limitaciones y diferencias frente a lo planificado; no las presentes como funciones terminadas.
5. Distingue lo implementado de lo planificado para la fase 2. No afirmes impactos sociales: el sistema mide lo registrado y aprobado.
6. Los datos de demostración no son resultados reales. No los cites como logros.
7. No incluyas contraseñas, llaves de API ni direcciones privadas de servicios.
8. Donde haya una marca [CAPTURA: ...], consérvala para que el equipo inserte la imagen.
```

## 3. Prompts para generar cada documento

### Informe final

```text
Con 01-como-quedo-el-sistema.md, 02-base-informe-final.md, casos-de-uso.md, derc.md y Fuese.md, redacta el informe final del proyecto SIRSE USAC. Sigue la estructura indicada en 02-base-informe-final.md. Incluye la matriz de cumplimiento completa, los cambios frente a lo planificado, las limitaciones y la alineación con FUESE. Marca como [PENDIENTE] todo dato contractual o de resultados que no esté en los archivos.
```

### Manual de usuario

```text
Con 01-como-quedo-el-sistema.md y 03-base-manual-usuario.md, redacta el manual de usuario de SIRSE USAC para personas sin conocimientos técnicos. Organízalo por rol, con pasos numerados, campos obligatorios, mensajes frecuentes y solución de problemas. Conserva las marcas [CAPTURA: ...] y usa los nombres de pantallas y botones tal como aparecen en la base.
```

### Manual técnico

```text
Con 01-como-quedo-el-sistema.md y 04-base-manual-tecnico.md, redacta el manual técnico de SIRSE USAC para el equipo que mantendrá el sistema. Incluye arquitectura, estructura del código, modelo de datos, seguridad, integraciones, instalación, despliegue, pruebas, mantenimiento y limitaciones conocidas. No incluyas valores secretos: nombra solo las variables de entorno.
```

## 4. Datos que debes conseguir o completar

Estos datos no están en el código y el proyecto los marcará como `[PENDIENTE]`:

- **Contractuales:** número de contrato, fechas, entregas, pagos y responsables. `docs/propuesta-economica.md` está citado en `CLAUDE.md`, pero no existe en el repositorio.
- **Institucionales:** autoridades de DIGEU y EPSUM, equipo de desarrollo, contrapartes y actas de aceptación.
- **Resultados reales:** cantidad de EPS, estudiantes, unidades, aporte y beneficiarios en producción. No uses los 320 EPS de demostración.
- **Pruebas con usuarios:** fechas, participantes, observaciones y correcciones, si se realizaron.
- **Producción:** URL oficial, responsable del hosting, política de respaldos y dueño de la llave de Google Maps.
- **Capturas de pantalla:** tómalas con datos de demostración, sin datos personales reales.
- **Decisiones por confirmar con DIGEU:** las que aparecen en la sección 8 de `02-base-informe-final.md`.

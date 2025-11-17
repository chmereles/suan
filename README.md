# SUAN — Módulo de Asistencia  
Sistema unificado de asistencia para empleados y planes municipales

Este módulo gestiona:

- Sincronización de fichadas desde dispositivos biométricos (CrossChex Cloud)
- Procesamiento de logs crudos por persona y vínculo laboral
- Generación de registros interpretados por jornada
- Producción de resúmenes diarios por vínculo laboral

La arquitectura distingue entre:

- **Personas** (`suan_people`)
- **Vínculos laborales** (`suan_labor_links`)
- **Fichadas crudas** (`attendance_logs`)
- **Fichadas interpretadas** (`suan_attendance_records`)
- **Resumen diario** (`suan_daily_summary`)

---

# 📘 Arquitectura General

El flujo completo de asistencia tiene 3 etapas:

```

1. CrossChex → attendance_logs (crudos)
2. attendance_logs → suan_attendance_records (interpretados)
3. suan_attendance_records → suan_daily_summary (resumen)

```

Cada etapa puede ejecutarse individualmente o como un pipeline completo.

---

# 📦 Comandos Disponibles

Este módulo define tres comandos principales:

- `suan:process-attendance`
- `suan:resolve-summary`
- `suan:process-day` (pipeline completo)

---

# 🟧 1. suan:process-attendance

```

php artisan suan:process-attendance [YYYY-MM-DD]

```

### ✔ Qué hace
Procesa fichadas a partir de los logs crudos almacenados en:

```

attendance_logs

```

y genera registros interpretados:

```

suan_attendance_records

```

### ✔ Cuándo usarlo
Utilizar cuando:

- Ya se sincronizó CrossChex.
- Desea **solo interpretar** las fichadas de un día.
- Se corrigieron logs manualmente y se desea reprocesarlos.
- En desarrollo, para re-ejecutar la etapa 2 sin tocar CrossChex.

### ✔ Qué NO hace
- ❌ No sincroniza CrossChex.
- ❌ No genera resúmenes diarios.

---

# 🟦 2. suan:resolve-summary

```

php artisan suan:resolve-summary [YYYY-MM-DD]

```

### ✔ Qué hace
Genera el resumen diario de asistencia por vínculo laboral:

```

suan_daily_summary

```

### ✔ Cuándo usarlo
Utilizar cuando:

- Ya se procesaron fichadas y se necesita recalcular el resumen.
- Se modificaron reglas de tardanza, ausencias o cálculo.
- En pruebas donde se desea recalcular solo el resumen (etapa 3).

### ✔ Qué NO hace
- ❌ No sincroniza CrossChex.
- ❌ No procesa fichadas.

---

# 🟩 3. suan:process-day (Pipeline completo)

```

php artisan suan:process-day [YYYY-MM-DD]

```

### ✔ Qué hace
Secuencia completa del día:

1. **Sincroniza** CrossChex → `attendance_logs`
2. **Procesa** fichadas → `suan_attendance_records`
3. **Genera** resumen diario → `suan_daily_summary`

### ✔ Cuándo usarlo
Este es el comando **principal para producción**:

- Ejecutado por cron cada madrugada.
- Para regenerar días completos.
- Para pruebas integrales del flujo.

### ✔ Qué NO hace
Todo lo hace. Este comando contiene a los otros dos.

---

# 🧩 Tabla Comparativa

| Comando | Sincroniza CrossChex | Procesa fichadas | Genera resumen | Caso ideal |
|---------|-----------------------|------------------|----------------|------------|
| `suan:process-attendance` | ❌ | ✔ | ❌ | Reprocesar logs ya existentes |
| `suan:resolve-summary` | ❌ | ❌ | ✔ | Recalcular resumen |
| `suan:process-day` | ✔ | ✔ | ✔ | Ejecución diaria completa (PROD) |

---

# 🧠 Preguntas Frecuentes

### ¿Cuál se usa en producción?  
**`suan:process-day`** ejecutado por cron.

### ¿Cuál uso si modifiqué fichadas a mano?  
`suan:process-attendance <fecha>`

### ¿Cuál uso si cambié reglas de resumen?  
`suan:resolve-summary <fecha>`

### ¿El pipeline completo hace falta correrlo más de una vez por día?  
No, solo si falló la sincronización o se corrigieron datos históricos.

---

# 🔧 Integración con Cron

Agregar una entrada:

```

0 3 * * * php /path/to/artisan suan:process-day >> /var/log/suan.log 2>&1

```

Ejecuta todos los días a las 03:00 AM.

---

# 🧱 Consideraciones de Modelado

### Personas (`suan_people`)
Representa a la persona única:

- documento
- nombre completo
- device_user_id

### Vínculos laborales (`suan_labor_links`)
Cada persona puede tener uno o más vínculos:

- Haberes
- Planes municipales
- Cargos múltiples
- Horas extras
- Contratos temporales

### Fichadas crudas (`attendance_logs`)
Se almacena todo lo recibido desde el reloj biométrico.

### Fichadas interpretadas (`suan_attendance_records`)
Resultado del procesamiento:

- horarios reales
- ingreso/egreso
- tardanza
- cortes
- jornada normalizada

### Resumen diario (`suan_daily_summary`)
Consolidación por vínculo laboral:

- horas trabajadas
- ausencias
- novedades
- tardanzas

---

# 📝 Recomendación final

- Usar **suan:process-day** como comando principal.
- Usar los otros dos solo para mantenimiento o depuración.
- No modificar manualmente `attendance_logs` salvo casos excepcionales.
- Ejecutar resúmenes solo después de procesar fichadas.

---

# 🏁 Contribuciones

Toda mejora, corrección o nueva funcionalidad debe incluir:

- migraciones actualizadas  
- documentación del comando  
- pruebas manuales sobre una fecha específica  


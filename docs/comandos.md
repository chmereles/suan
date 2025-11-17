A continuación tenés la **documentación oficial**, clara y profesional, de **todos los comandos de asistencia SUAN**, indicando:

* **qué hace cada uno**
* **cuándo usarlo**
* **cuál es redundante**
* **cuál NO se usa más**
* recomendaciones operativas

También podés pegarlo en tu README o Wiki interna.

---

# 📘 **📌 DOCUMENTACIÓN OFICIAL — COMANDOS DE ASISTENCIA SUAN**

La arquitectura de asistencia de SUAN tiene **tres etapas**:

---

# 🟧 **1) Sincronización de fichadas crudas**

Guarda datos desde CrossChex en `attendance_logs`.

# 🟦 **2) Procesamiento de fichadas**

Interpreta los logs por persona + vínculo laboral → `suan_attendance_records`.

# 🟩 **3) Resumen diario por vínculo laboral**

Genera entradas en `suan_daily_summary`.

---

Cada comando corresponde a una etapa o a un pipeline completo.

---

# 🟧 1. **suan:process-attendance**

```bash
php artisan suan:process-attendance [YYYY-MM-DD]
```

### ✔ ¿Qué hace?

Procesa SOLO fichadas, tomando lo que ya está almacenado en:

```
attendance_logs
```

y generando:

```
suan_attendance_records
```

### ✔ ¿Cuándo usarlo?

* Cuando **ya se sincronizó CrossChex** y querés **solo interpretar los logs**.
* Cuando se hicieron correcciones manuales en `attendance_logs`.
* En pruebas de desarrollo para re-procesar un día sin sincronizar.

### ✔ NO sincroniza.

Solo procesa lo que YA existe en la base.

### ✔ Útil para debugging.

Permite re-ejecutar el paso 2 sin tocar CrossChex.

---

# 🟦 2. **suan:resolve-summary**

```bash
php artisan suan:resolve-summary [YYYY-MM-DD]
```

### ✔ ¿Qué hace?

Genera:

```
suan_daily_summary
```

por cada **vínculo laboral** activo (`suan_labor_links`).

### ✔ ¿Cuándo usarlo?

* Cuando ya procesaste fichadas pero querés regenerar resúmenes.
* Cuando cambiaste reglas de resumen (tardanza, ausencias, etc.).
* Cuando estás desarrollando y necesitás recalcular los resúmenes.

### ✔ NO procesa logs.

NO sincroniza CrossChex.
NO genera registros de asistencia.
SOLO genera resúmenes.

---

# 🟩 3. **suan:process-day**

```bash
php artisan suan:process-day [YYYY-MM-DD]
```

🔥 **Este es el PIPELINE COMPLETO.**

### ✔ ¿Qué hace?

Secuencia completa:

1. **Sincroniza CrossChex → attendance_logs**
2. **Procesa todas las fichadas → suan_attendance_records**
3. **Genera resumen diario → suan_daily_summary**

### ✔ ¿Cuándo usarlo?

Este es el **comando principal para uso diario**.

* Se ejecuta por **cron** cada madrugada.
* También se usa cuando querés regenerar todo un día completo.

### ✔ Puede reemplazar a los otros dos.

Porque los incluye dentro.

### ✔ Recomendado para producción.

Es el comando estándar para “correr el día completo”.

---

# 🚨 Tabla comparativa rápida

| Comando                     | Sincroniza CrossChex | Procesa fichadas | Genera resumen | Caso ideal                    |
| --------------------------- | -------------------- | ---------------- | -------------- | ----------------------------- |
| **suan:process-attendance** | ❌                    | ✔                | ❌              | Reprocesar logs ya existentes |
| **suan:resolve-summary**    | ❌                    | ❌                | ✔              | Volver a generar resúmenes    |
| **suan:process-day**        | ✔                    | ✔                | ✔              | Flujo completo del día (PROD) |

---

# 🎯 **¿Alguno es redundante?**

### ✔ **NO**

pero **suan:process-day** *incluye* los otros dos.

Los otros dos se mantienen por:

* depuración
* re-procesamiento puntual
* migraciones
* soporte y mantenimiento manual

---

# 🧠 ¿Cuál usar normalmente?

### **Producción diaria:**

👉 `suan:process-day`

### **Cuando corregiste manualmente fichadas o vinculación laboral:**

👉 `suan:process-attendance <fecha>`

### **Cuando ajustaste reglas de resumen:**

👉 `suan:resolve-summary <fecha>`

---

# 🛠 ¿Querés que ahora genere un archivo README.md final con esta documentación lista para subir al repositorio?

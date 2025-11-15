<!-- resources/js/Components/Attendance/AttendanceSummaryTable.vue -->
<script setup lang="ts">
import { computed } from 'vue'
import type { DailySummary } from '@/types/attendance'

const props = defineProps<{
  summaries: DailySummary[]
  loading?: boolean
}>()

const rows = computed(() =>
  props.summaries.map((s) => ({
    id: s.id,
    legajo: s.employee?.legajo ?? s.employee_id,
    nombre: s.employee?.full_name ?? '—',
    area: s.employee?.area ?? '—',
    status: s.status,
    worked: s.total_worked_minutes,
    justified: s.justified,
    notes: s.notes,
  }))
)

const statusLabel: Record<string, string> = {
  present: 'Presente',
  absent_unjustified: 'Ausencia injustificada',
  absent_justified: 'Ausencia justificada',
  license: 'Licencia',
  holiday: 'Feriado',
  anomaly: 'Anomalía',
}

const statusClass = (status: string) => {
  switch (status) {
    case 'present':
      return 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-100'
    case 'absent_unjustified':
      return 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-100'
    case 'absent_justified':
      return 'bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-100'
    case 'license':
      return 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-100'
    case 'holiday':
      return 'bg-sky-100 text-sky-800 dark:bg-sky-900/50 dark:text-sky-100'
    case 'anomaly':
      return 'bg-fuchsia-100 text-fuchsia-800 dark:bg-fuchsia-900/50 dark:text-fuchsia-100'
    default:
      return 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100'
  }
}
</script>

<template>
  <div class="mt-6 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
    <div class="flex items-center justify-between px-4 py-3">
      <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-100">
        Detalle por agente
      </h2>
      <span class="text-xs text-gray-500 dark:text-gray-400">
        {{ rows.length }} registros
      </span>
    </div>

    <div class="overflow-x-auto border-t border-gray-100 dark:border-gray-700">
      <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-800/70">
          <tr>
            <th class="px-4 py-2 text-left font-medium text-gray-600 dark:text-gray-300">Legajo</th>
            <th class="px-4 py-2 text-left font-medium text-gray-600 dark:text-gray-300">Nombre</th>
            <th class="px-4 py-2 text-left font-medium text-gray-600 dark:text-gray-300">Área</th>
            <th class="px-4 py-2 text-left font-medium text-gray-600 dark:text-gray-300">Estado</th>
            <th class="px-4 py-2 text-right font-medium text-gray-600 dark:text-gray-300">Horas</th>
            <th class="px-4 py-2 text-center font-medium text-gray-600 dark:text-gray-300">Justif.</th>
            <th class="px-4 py-2 text-left font-medium text-gray-600 dark:text-gray-300">Notas</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 bg-white dark:divide-gray-800 dark:bg-gray-900/40">
          <tr v-if="loading">
            <td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
              Cargando información...
            </td>
          </tr>

          <tr v-else-if="rows.length === 0">
            <td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
              No hay registros para la fecha seleccionada.
            </td>
          </tr>

          <tr v-for="row in rows" :key="row.id" class="hover:bg-gray-50/60 dark:hover:bg-gray-800/60">
            <td class="whitespace-nowrap px-4 py-2 text-gray-800 dark:text-gray-100">
              {{ row.legajo }}
            </td>
            <td class="px-4 py-2 text-gray-800 dark:text-gray-100">
              {{ row.nombre }}
            </td>
            <td class="px-4 py-2 text-gray-600 dark:text-gray-300">
              {{ row.area }}
            </td>
            <td class="px-4 py-2">
              <span
                class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold"
                :class="statusClass(row.status)"
              >
                {{ statusLabel[row.status] ?? row.status }}
              </span>
            </td>
            <td class="px-4 py-2 text-right text-gray-800 dark:text-gray-100">
              {{ (row.worked || 0) / 60 | 0 }} h
            </td>
            <td class="px-4 py-2 text-center">
              <span
                v-if="row.justified"
                class="inline-flex rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-100"
              >
                Sí
              </span>
              <span
                v-else
                class="inline-flex rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold text-gray-700 dark:bg-gray-800 dark:text-gray-200"
              >
                No
              </span>
            </td>
            <td class="px-4 py-2 text-xs text-gray-600 dark:text-gray-300">
              {{ row.notes || '—' }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

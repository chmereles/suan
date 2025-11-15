<!-- resources/js/Components/Attendance/AttendanceSummaryCards.vue -->
<script setup lang="ts">
import { computed } from 'vue'
import type { DailySummary } from '@/types/attendance'

const props = defineProps<{
  summaries: DailySummary[]
}>()

const total = computed(() => props.summaries.length)

const byStatus = computed(() => {
  const base = {
    present: 0,
    absent_unjustified: 0,
    absent_justified: 0,
    license: 0,
    holiday: 0,
    anomaly: 0,
  }

  for (const s of props.summaries) {
    base[s.status] = (base[s.status] ?? 0) + 1
  }
  return base
})

const totalHours = computed(() => {
  const minutes = props.summaries.reduce(
    (acc, s) => acc + (s.total_worked_minutes || 0),
    0
  )
  return (minutes / 60).toFixed(1)
})
</script>

<template>
  <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
    <!-- Total -->
    <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
      <div class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
        Total de agentes
      </div>
      <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">
        {{ total }}
      </div>
    </div>

    <!-- Presentes -->
    <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-4 shadow-sm dark:border-emerald-700/40 dark:bg-emerald-900/40">
      <div class="text-xs font-semibold uppercase tracking-wide text-emerald-700 dark:text-emerald-200">
        Presentes
      </div>
      <div class="mt-2 text-3xl font-bold text-emerald-900 dark:text-emerald-50">
        {{ byStatus.present }}
      </div>
    </div>

    <!-- Ausencias injustificadas -->
    <div class="rounded-2xl border border-red-100 bg-red-50 p-4 shadow-sm dark:border-red-700/40 dark:bg-red-900/40">
      <div class="text-xs font-semibold uppercase tracking-wide text-red-700 dark:text-red-200">
        Ausencias injustificadas
      </div>
      <div class="mt-2 text-3xl font-bold text-red-900 dark:text-red-50">
        {{ byStatus.absent_unjustified }}
      </div>
    </div>

    <!-- Licencias / Anomalías resumidas -->
    <div class="rounded-2xl border border-indigo-100 bg-indigo-50 p-4 shadow-sm dark:border-indigo-700/40 dark:bg-indigo-900/40">
      <div class="text-xs font-semibold uppercase tracking-wide text-indigo-700 dark:text-indigo-200">
        Licencias / Anomalías
      </div>
      <div class="mt-2 flex items-baseline gap-4">
        <div>
          <div class="text-xs text-indigo-600/80 dark:text-indigo-200/80">Licencias</div>
          <div class="text-2xl font-semibold text-indigo-900 dark:text-indigo-50">
            {{ byStatus.license }}
          </div>
        </div>
        <div>
          <div class="text-xs text-indigo-600/80 dark:text-indigo-200/80">Anomalías</div>
          <div class="text-2xl font-semibold text-indigo-900 dark:text-indigo-50">
            {{ byStatus.anomaly }}
          </div>
        </div>
      </div>
      <div class="mt-2 text-xs text-indigo-800/80 dark:text-indigo-100/80">
        Horas trabajadas totales: <span class="font-semibold">{{ totalHours }}</span> h
      </div>
    </div>
  </div>
</template>

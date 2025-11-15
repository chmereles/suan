<!-- resources/js/Pages/Attendance/Dashboard.vue -->
<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import DateFilterBar from '@/components/Attendance/DateFilterBar.vue'
import AttendanceSummaryCards from '@/components/Attendance/AttendanceSummaryCards.vue'
import AttendanceSummaryTable from '@/components/Attendance/AttendanceSummaryTable.vue'
import type { DailySummary } from '@/types/attendance'

const summaries = ref<DailySummary[]>([])
const selectedDate = ref<string>(new Date().toISOString().slice(0, 10))
const loading = ref(false)
const errorMessage = ref<string | null>(null)

const loadSummary = async () => {
  try {
    loading.value = true
    errorMessage.value = null

    const resp = await fetch(`/api/suan/attendance/summary?date=${selectedDate.value}`, {
      headers: { Accept: 'application/json' },
    })

    if (!resp.ok) {
      throw new Error(`Error HTTP ${resp.status}`)
    }

    const data = await resp.json()
    summaries.value = data.summaries ?? []
  } catch (error: any) {
    console.error(error)
    errorMessage.value = 'No se pudo cargar la información de asistencia.'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadSummary()
})

watch(selectedDate, () => {
  loadSummary()
})
</script>

<template>
  <AppLayout title="Asistencia diaria">
    <div class="space-y-6">
      <!-- Encabezado -->
      <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
            Asistencia diaria
          </h1>
          <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Resumen de presentismo y novedades para la fecha seleccionada.
          </p>
        </div>
      </div>

      <!-- Filtros -->
      <DateFilterBar
        v-model="selectedDate"
        :loading="loading"
        @refresh="loadSummary"
      />

      <!-- Mensaje de error -->
      <div
        v-if="errorMessage"
        class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-700/60 dark:bg-red-900/30 dark:text-red-100"
      >
        {{ errorMessage }}
      </div>

      <!-- Resumen -->
      <AttendanceSummaryCards :summaries="summaries" />

      <!-- Tabla -->
      <AttendanceSummaryTable :summaries="summaries" :loading="loading" />
    </div>
  </AppLayout>
</template>

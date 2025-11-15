<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { format, parseISO } from 'date-fns'
import { es } from 'date-fns/locale'
import type { DailySummary } from '@/types/attendance'
import DateFilterBar from '@/components/Attendance/DateFilterBar.vue'
import AttendanceSummaryCards from '@/components/Attendance/AttendanceSummaryCards.vue'
import AttendanceSummaryTable from '@/components/Attendance/AttendanceSummaryTable.vue'
import AppLayout from '@/layouts/AppLayout.vue'

// ---------------------------------------------
// PROPS con tipado limpio y reutilizable
// ---------------------------------------------
const props = defineProps<{
  date: string
  summaries: DailySummary[]
}>()

// Date local
const selectedDate = ref(props.date)

function changeDate() {
  router.get(
    '/attendance',
    { date: selectedDate.value },
    {
      preserveState: true,
      preserveScroll: true,
    }
  )
}

function formatMinutes(min: number | null) {
  if (!min) return '0 h'
  const h = Math.floor(min / 60)
  const m = min % 60
  return `${h} h ${m} min`
}

function formatDate(d: string) {
  return format(parseISO(d), "dd 'de' MMMM yyyy", { locale: es })
}
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
      <DateFilterBar v-model="selectedDate" @update:modelValue="changeDate" />


      <!-- Mensaje de error -->
      <div v-if="Object.keys($page.props.errors).length">
        <p class="text-red-500">
          {{ $page.props.errors }}
        </p>
      </div>

      <!-- Resumen -->
      <AttendanceSummaryCards :summaries="summaries" />

      <!-- Tabla -->
      <AttendanceSummaryTable :summaries="summaries" />
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { format, parseISO } from 'date-fns'
import { es } from 'date-fns/locale'
import type { DailySummary } from '@/types/attendance'

import DateFilterBar from '@/components/Attendance/DateFilterBar.vue'
import AttendanceSummaryCards from '@/components/Attendance/AttendanceSummaryCards.vue'
import AttendanceSummaryTable from '@/components/Attendance/AttendanceSummaryTable.vue'

const props = defineProps<{
  date: string
  summaries: DailySummary[]
}>()

const selectedDate = ref(props.date)

watch(selectedDate, (newVal) => {
  router.get('/attendance', { date: newVal }, {
    preserveState: true,
    preserveScroll: true,
  })
})

</script>

<template>
  <AppLayout title="Asistencia diaria">
    <div class="space-y-6">

      <div>
        <h1 class="text-xl font-semibold">Asistencia del día</h1>
        <p class="text-gray-600">Resumen consolidado de presentismo</p>
      </div>

      <DateFilterBar v-model="selectedDate" />

      <AttendanceSummaryCards :summaries="props.summaries" />

      <AttendanceSummaryTable :summaries="props.summaries" />
    </div>
  </AppLayout>
</template>

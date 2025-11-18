<script setup lang="ts">
import StatusBadge from './StatusBadge.vue'
import AttendanceAnomaliesBadge from './AttendanceAnomaliesBadge.vue'
import type { DailySummary } from '@/types/attendance'

const props = defineProps<{
  summary: DailySummary
}>()

const workedHours = Math.floor((props.summary.worked_minutes ?? 0) / 60)
</script>

<template>
  <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
    <td class="px-4 py-2 text-gray-800 dark:text-gray-100">
      {{ props.summary.labor_link.person?.document ?? props.summary.labor_link_id }}
    </td>

    <td class="px-4 py-2 text-gray-900 font-medium dark:text-gray-100">
      {{ props.summary.labor_link.person?.full_name ?? '—' }}
    </td>

    <td class="px-4 py-2 text-gray-600 dark:text-gray-300">
      <!-- {{ props.summary.employee?.area ?? '—' }} -->
    </td>

    <td class="px-4 py-2">
      <StatusBadge :status="props.summary.status" />
    </td>

    <td class="px-4 py-2 text-right text-gray-800 dark:text-gray-100">
      {{ workedHours }} h
    </td>

    <td class="px-4 py-2 text-center">
      <AttendanceAnomaliesBadge :anomalies="props.summary.anomalies" />
    </td>

    <td class="px-4 py-2 text-xs text-gray-600 dark:text-gray-300">
      {{ props.summary.notes || '—' }}
    </td>
  </tr>
</template>

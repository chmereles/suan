<script setup lang="ts">
import { computed } from 'vue'
import { AlertTriangle, CheckCircle, XCircle, Clock, Umbrella } from 'lucide-vue-next'

const props = defineProps<{
  status: string
}>()

const config = computed(() => {
  switch (props.status) {
    case 'present':
      return {
        label: 'Presente',
        icon: CheckCircle,
        classes: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200'
      }

    case 'absent_unjustified':
      return {
        label: 'Ausencia injustificada',
        icon: XCircle,
        classes: 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-200'
      }

    case 'absent_justified':
      return {
        label: 'Ausencia justificada',
        icon: Clock,
        classes: 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200'
      }

    case 'partial':
      return {
        label: 'Jornada parcial',
        icon: Clock,
        classes: 'bg-sky-100 text-sky-800 dark:bg-sky-900/40 dark:text-sky-200'
      }

    case 'license':
      return {
        label: 'Licencia',
        icon: Umbrella,
        classes: 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-200'
      }

    case 'holiday':
      return {
        label: 'Feriado',
        icon: Umbrella,
        classes: 'bg-sky-100 text-sky-800 dark:bg-sky-900/40 dark:text-sky-200'
      }

    case 'anomaly':
      return {
        label: 'Anomalía',
        icon: AlertTriangle,
        classes: 'bg-fuchsia-100 text-fuchsia-800 dark:bg-fuchsia-900/40 dark:text-fuchsia-200'
      }

    default:
      return {
        label: props.status,
        icon: Clock,
        classes: 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200'
      }
  }
})
</script>

<template>
  <span
    class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium"
    :class="config.classes"
  >
    <component :is="config.icon" class="h-3.5 w-3.5" />
    {{ config.label }}
  </span>
</template>

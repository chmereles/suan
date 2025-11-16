<script setup lang="ts">
import { Info, AlertTriangle } from 'lucide-vue-next'

const props = defineProps<{
  anomalies: any[] | null
}>()

const count = props.anomalies?.length ?? 0
</script>

<template>
  <div v-if="count > 0" class="group relative inline-flex items-center">
    <AlertTriangle class="h-4 w-4 text-fuchsia-600 dark:text-fuchsia-300" />

    <span class="ml-1 text-xs text-fuchsia-700 dark:text-fuchsia-300">
      {{ count }}
    </span>

    <!-- Tooltip -->
    <div
      class="absolute left-1/2 top-full z-20 hidden w-56 -translate-x-1/2 rounded-lg border border-gray-200 bg-white p-2 text-xs text-gray-700 shadow-lg group-hover:block dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200"
    >
      <div class="mb-1 font-semibold text-fuchsia-700 dark:text-fuchsia-300">
        Anomalías detectadas:
      </div>

      <ul class="list-disc pl-4">
        <li v-for="a in anomalies" :key="a.type" class="mb-1">
          <span class="font-semibold">{{ a.type }}</span>
          <span v-if="a.minutes !== undefined"> ({{ a.minutes }} min)</span>
          <div class="text-gray-500 dark:text-gray-400">
            {{ a.description ?? '' }}
          </div>
        </li>
      </ul>
    </div>
  </div>
</template>

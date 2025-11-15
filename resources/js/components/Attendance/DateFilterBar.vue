<!-- resources/js/Components/Attendance/DateFilterBar.vue -->
<script setup lang="ts">
import { defineEmits, defineProps } from 'vue'

const props = defineProps<{
  modelValue: string
  loading?: boolean
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: string): void
  (e: 'refresh'): void
}>()

const onInput = (event: Event) => {
  const target = event.target as HTMLInputElement
  emit('update:modelValue', target.value)
}
</script>

<template>
  <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
    <div class="flex flex-col">
      <label class="text-sm font-medium text-gray-700 dark:text-gray-200">
        Fecha
      </label>
      <input
        type="date"
        :value="modelValue"
        @input="onInput"
        class="mt-1 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
      />
    </div>

    <button
      type="button"
      @click="$emit('refresh')"
      :disabled="loading"
      class="mt-2 inline-flex items-center justify-center rounded-lg border border-transparent px-4 py-2 text-sm font-medium shadow-sm transition
             disabled:cursor-not-allowed disabled:opacity-60
             bg-blue-600 text-white hover:bg-blue-700
             dark:bg-blue-500 dark:hover:bg-blue-600"
    >
      <span v-if="!loading">Actualizar</span>
      <span v-else>Actualizando...</span>
    </button>
  </div>
</template>

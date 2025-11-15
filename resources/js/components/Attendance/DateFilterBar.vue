<script setup lang="ts">
import { defineProps, defineEmits } from 'vue'

const props = defineProps<{
  modelValue: string
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: string): void
}>()

function onChange(e: Event) {
  const value = (e.target as HTMLInputElement).value
  emit('update:modelValue', value)
}
</script>

<template>
  <div class="flex items-center gap-3">
    <input
      type="date"
      :value="modelValue"
      @input="onChange"
      class="border px-3 py-2 rounded-md dark:bg-gray-800 dark:text-gray-200"
    />
  </div>
</template>

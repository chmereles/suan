<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3'
import { computed } from 'vue'

import { store as contextEventStore } from '@/routes/attendance/context-event';

const props = defineProps<{
  date: string
  employeeId?: number
  employees?: { id: number; full_name: string; legajo: string }[]
}>()

const form = useForm({
  employee_id: props.employeeId ?? null,
  date: props.date,
  type: 'rrhh_note',
  description: '',
})

const employeeOptions = computed(() => props.employees ?? [])

</script>

<template>
  <AppLayout title="Registrar nota externa">
    <div class="max-w-xl space-y-6">
      <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
        Registrar nota externa
      </h1>

      <form
        class="space-y-4 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900"
        @submit.prevent="form.post(contextEventStore().url)"
      >
        <div class="space-y-1">
          <label class="text-sm font-medium text-gray-700 dark:text-gray-200">
            Empleado
          </label>
          <select
            v-model="form.employee_id"
            class="block w-full rounded-md border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
          >
            <option disabled value="">Seleccionar empleado…</option>
            <option
              v-for="e in employeeOptions"
              :key="e.id"
              :value="e.id"
            >
              {{ e.legajo }} - {{ e.full_name }}
            </option>
          </select>
          <InputError :message="form.errors.employee_id" />
        </div>

        <div class="space-y-1">
          <label class="text-sm font-medium text-gray-700 dark:text-gray-200">
            Fecha
          </label>
          <input
            type="date"
            v-model="form.date"
            class="block w-full rounded-md border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
          />
          <InputError :message="form.errors.date" />
        </div>

        <div class="space-y-1">
          <label class="text-sm font-medium text-gray-700 dark:text-gray-200">
            Tipo de nota
          </label>
          <select
            v-model="form.type"
            class="block w-full rounded-md border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
          >
            <option value="rrhh_note">Nota RRHH</option>
            <option value="supervisor_note">Nota de jefe</option>
            <option value="commission">Comisión de servicio</option>
            <option value="training">Capacitación</option>
            <option value="legacy_note">Nota heredada</option>
          </select>
          <InputError :message="form.errors.type" />
        </div>

        <div class="space-y-1">
          <label class="text-sm font-medium text-gray-700 dark:text-gray-200">
            Descripción
          </label>
          <textarea
            v-model="form.description"
            rows="3"
            class="block w-full rounded-md border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
          />
          <InputError :message="form.errors.description" />
        </div>

        <div class="flex justify-end gap-2">
          <button
            type="submit"
            class="inline-flex items-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 disabled:opacity-50"
            :disabled="form.processing"
          >
            Guardar nota
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>

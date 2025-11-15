<!-- resources/js/Pages/Attendance/Sync/Index.vue -->
<script setup lang="ts">
import { computed } from 'vue'
import { Head, router, useForm, usePage } from '@inertiajs/vue3'

// Rutas
import { run as attenddanceSyncRun } from '@/routes/attendance/sync'

type SyncLog = {
  id: number
  source: string
  triggered_by: string
  window_minutes: number | null
  inserted_count: number
  status: 'running' | 'success' | 'failed' | string
  error_message: string | null
  started_at: string | null
  finished_at: string | null
  duration_sec: number | null
}

type LastSync = {
  status: string
  inserted_count: number
  started_at: string | null
  finished_at: string | null
  window_minutes: number | null
} | null

const props = defineProps<{
  logs: {
    data: SyncLog[]
    links: { url: string | null; label: string; active: boolean }[]
  }
  lastSync: LastSync
  defaultWindow: number
  cronInfo: {
    expected_interval_minutes: number
    last_cron_run_at: string | null
  }
  canRunSync: boolean
}>()

const page = usePage<{
  flash: { success?: string; error?: string }
}>()

const form = useForm({
  window: props.defaultWindow,
})

const syncing = computed(() => form.processing)

const runSync = () => {
  form.post(attenddanceSyncRun().url, {
    preserveScroll: true,
  })
}

const statusBadgeClass = (status: string) => {
  switch (status) {
    case 'success':
      return 'inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800'
    case 'failed':
      return 'inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-800'
    case 'running':
      return 'inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded-full bg-amber-100 text-amber-800'
    default:
      return 'inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded-full bg-slate-100 text-slate-800'
  }
}
</script>

<template>
  <Head title="Sincronización de asistencia" />

  <div class="space-y-6">
    <!-- Mensajes flash -->
    <div v-if="page.props.flash?.success" class="rounded-md bg-emerald-50 p-3 text-sm text-emerald-800">
      {{ page.props.flash.success }}
    </div>
    <div v-if="page.props.flash?.error" class="rounded-md bg-red-50 p-3 text-sm text-red-800">
      {{ page.props.flash.error }}
    </div>

    <!-- Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-xl font-semibold text-slate-900">Sincronización de asistencia (CrossChex)</h1>
        <p class="text-sm text-slate-500">
          Panel para monitorear y ejecutar sincronizaciones de fichadas desde CrossChex Cloud.
        </p>
      </div>
      <div class="text-xs text-slate-500">
        <div>Última ejecución por CRON: {{ cronInfo.last_cron_run_at ?? 'Sin datos' }}</div>
        <div>Intervalo esperado: {{ cronInfo.expected_interval_minutes }} min</div>
      </div>
    </div>

    <!-- Tarjeta de estado principal -->
    <div class="grid gap-4 sm:grid-cols-3">
      <!-- Última sincronización -->
      <div class="rounded-xl border bg-white p-4 shadow-sm sm:col-span-2">
        <h2 class="mb-2 text-sm font-medium text-slate-700">Última sincronización</h2>

        <div v-if="lastSync" class="space-y-1 text-sm">
          <div class="flex items-center gap-2">
            <span class="text-slate-500">Estado:</span>
            <span :class="statusBadgeClass(lastSync.status)">
              {{ lastSync.status === 'success' ? 'Exitosa' : lastSync.status === 'failed' ? 'Fallida' : 'En curso' }}
            </span>
          </div>
          <div>
            <span class="text-slate-500">Inicio:</span>
            <span class="ml-1">{{ lastSync.started_at ?? '—' }}</span>
          </div>
          <div>
            <span class="text-slate-500">Fin:</span>
            <span class="ml-1">{{ lastSync.finished_at ?? '—' }}</span>
          </div>
          <div>
            <span class="text-slate-500">Registros nuevos:</span>
            <span class="ml-1 font-semibold">{{ lastSync.inserted_count }}</span>
          </div>
          <div>
            <span class="text-slate-500">Ventana:</span>
            <span class="ml-1">{{ lastSync.window_minutes ?? 'por defecto' }} min</span>
          </div>
        </div>
        <div v-else class="text-sm text-slate-500">
          Aún no se ha ejecutado ninguna sincronización.
        </div>
      </div>

      <!-- Acción: Sincronizar ahora -->
      <div class="rounded-xl border bg-white p-4 shadow-sm">
        <h2 class="mb-2 text-sm font-medium text-slate-700">Acciones</h2>

        <div class="space-y-3 text-sm">
          <div class="space-y-1">
            <label for="window" class="block text-xs font-medium text-slate-600">
              Ventana de sincronización (minutos)
            </label>
            <input
              id="window"
              v-model.number="form.window"
              type="number"
              min="1"
              class="block w-full rounded-md border border-slate-300 px-2 py-1 text-sm shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
            />
            <p class="text-xs text-slate-500">
              Se sincronizan las fichadas dentro de los últimos N minutos. Dejar vacío para usar el valor por defecto.
            </p>
          </div>

          <button
            v-if="canRunSync"
            type="button"
            :disabled="syncing"
            @click="runSync"
            class="inline-flex w-full items-center justify-center rounded-md bg-sky-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-sky-700 disabled:cursor-not-allowed disabled:bg-sky-300"
          >
            <span v-if="!syncing">Sincronizar ahora</span>
            <span v-else>Sincronizando…</span>
          </button>
          <p v-else class="text-xs text-red-500">
            No tenés permisos para ejecutar la sincronización manual.
          </p>
        </div>
      </div>
    </div>

    <!-- Historial de sincronizaciones -->
    <div class="rounded-xl border bg-white p-4 shadow-sm">
      <h2 class="mb-3 text-sm font-medium text-slate-700">Historial de sincronizaciones</h2>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
          <thead>
            <tr class="bg-slate-50">
              <th class="px-3 py-2 text-left text-xs font-medium uppercase tracking-wide text-slate-500">Fecha inicio</th>
              <th class="px-3 py-2 text-left text-xs font-medium uppercase tracking-wide text-slate-500">Disparado por</th>
              <th class="px-3 py-2 text-right text-xs font-medium uppercase tracking-wide text-slate-500">Registros</th>
              <th class="px-3 py-2 text-right text-xs font-medium uppercase tracking-wide text-slate-500">Duración (s)</th>
              <th class="px-3 py-2 text-left text-xs font-medium uppercase tracking-wide text-slate-500">Estado</th>
              <th class="px-3 py-2 text-left text-xs font-medium uppercase tracking-wide text-slate-500">Error</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-200">
            <tr v-for="log in props.logs.data" :key="log.id">
              <td class="px-3 py-2 whitespace-nowrap">{{ log.started_at ?? '—' }}</td>
              <td class="px-3 py-2 whitespace-nowrap">{{ log.triggered_by }}</td>
              <td class="px-3 py-2 whitespace-nowrap text-right">{{ log.inserted_count }}</td>
              <td class="px-3 py-2 whitespace-nowrap text-right">
                {{ log.duration_sec !== null ? log.duration_sec : '—' }}
              </td>
              <td class="px-3 py-2 whitespace-nowrap">
                <span :class="statusBadgeClass(log.status)">
                  {{ log.status === 'success' ? 'Exitosa' : log.status === 'failed' ? 'Fallida' : 'En curso' }}
                </span>
              </td>
              <td class="max-w-xs px-3 py-2 whitespace-nowrap text-xs text-red-600">
                <span v-if="log.error_message" class="line-clamp-1">
                  {{ log.error_message }}
                </span>
                <span v-else>—</span>
              </td>
            </tr>
            <tr v-if="props.logs.data.length === 0">
              <td colspan="6" class="px-3 py-4 text-center text-sm text-slate-500">
                No hay sincronizaciones registradas aún.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Paginación simple (podés reemplazar por tu componente de paginación) -->
      <div class="mt-3 flex flex-wrap gap-1 text-xs">
        <button
          v-for="link in props.logs.links"
          :key="link.label"
          type="button"
          :disabled="!link.url"
          @click="link.url && router.visit(link.url, { preserveScroll: true })"
          class="rounded px-2 py-1"
          :class="[
            link.active
              ? 'bg-sky-600 text-white'
              : link.url
                ? 'bg-slate-100 text-slate-700 hover:bg-slate-200'
                : 'text-slate-400'
          ]"
          v-html="link.label"
        />
      </div>
    </div>
  </div>
</template>

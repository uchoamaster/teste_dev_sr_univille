<script setup>
import { reactive, watch } from 'vue'

const props = defineProps({
  filters: {
    type: Object,
    required: true,
  },
  loading: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['apply', 'reset'])

const localFilters = reactive({ ...props.filters })

watch(
  () => props.filters,
  (value) => {
    Object.assign(localFilters, value)
  },
  { deep: true },
)

function onSubmit() {
  emit('apply', { ...localFilters })
}

function onReset() {
  Object.assign(localFilters, {
    status: '',
    start_date: '',
    end_date: '',
    min_amount: '',
    max_amount: '',
  })

  emit('reset')
}
</script>

<template>
  <section class="slide-up rounded-[24px] border border-slate-200 bg-white p-4 shadow-sm shadow-slate-200/70 sm:p-5">
    <div class="mb-4">
      <div>
        <h2 class="text-lg font-semibold tracking-[-0.03em] text-slate-950 sm:text-xl">Filtros operacionais</h2>
        <p class="mt-1 text-sm text-slate-500">Filtre por status, periodo e faixa de valor.</p>
      </div>
    </div>

    <form class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5" @submit.prevent="onSubmit">
      <label class="flex min-w-0 flex-col gap-2 text-sm font-medium text-slate-600">
        Status
        <select v-model="localFilters.status" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-teal-500">
          <option value="">Todos</option>
          <option value="pending">Pending</option>
          <option value="processed">Processed</option>
          <option value="failed">Failed</option>
          <option value="invalid">Invalid</option>
        </select>
      </label>

      <label class="flex min-w-0 flex-col gap-2 text-sm font-medium text-slate-600">
        Inicio
        <input v-model="localFilters.start_date" type="date" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-teal-500" />
      </label>

      <label class="flex min-w-0 flex-col gap-2 text-sm font-medium text-slate-600">
        Fim
        <input v-model="localFilters.end_date" type="date" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-teal-500" />
      </label>

      <label class="flex min-w-0 flex-col gap-2 text-sm font-medium text-slate-600">
        Valor minimo
        <input v-model="localFilters.min_amount" type="number" min="0" step="0.01" placeholder="0.00" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-teal-500" />
      </label>

      <label class="flex min-w-0 flex-col gap-2 text-sm font-medium text-slate-600">
        Valor maximo
        <input v-model="localFilters.max_amount" type="number" min="0" step="0.01" placeholder="9999.99" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-teal-500" />
      </label>

      <div class="flex flex-col gap-2 pt-1 sm:col-span-2 sm:flex-row sm:justify-end xl:col-span-5">
        <button type="button" class="rounded-2xl border border-slate-300 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-slate-100" @click="onReset">
          Limpar
        </button>
        <button type="submit" :disabled="loading" class="rounded-2xl bg-slate-950 px-4 py-3 text-sm font-semibold text-white transition hover:bg-teal-700 disabled:cursor-not-allowed disabled:bg-slate-400">
          {{ loading ? 'Aplicando...' : 'Aplicar filtros' }}
        </button>
      </div>
    </form>
  </section>
</template>
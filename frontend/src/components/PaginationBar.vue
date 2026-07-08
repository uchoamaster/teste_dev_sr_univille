<script setup>
const props = defineProps({
  meta: {
    type: Object,
    required: true,
  },
  loading: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['change'])

function goTo(page) {
  if (page < 1 || page > props.meta.last_page || page === props.meta.current_page || props.loading) {
    return
  }

  emit('change', page)
}
</script>

<template>
  <div class="slide-up flex flex-col gap-4 rounded-[24px] border border-slate-200/70 bg-white/80 px-5 py-4 shadow-lg shadow-slate-900/5 backdrop-blur sm:flex-row sm:items-center sm:justify-between">
    <p class="text-sm text-slate-500">
      Exibindo {{ meta.from || 0 }}-{{ meta.to || 0 }} de {{ meta.total || 0 }} registros.
    </p>

    <div class="flex items-center gap-2">
      <button class="rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-40" :disabled="meta.current_page <= 1 || loading" @click="goTo(meta.current_page - 1)">
        Anterior
      </button>
      <span class="mono rounded-full bg-slate-950 px-4 py-2 text-xs font-semibold uppercase tracking-[0.24em] text-white">
        {{ meta.current_page }} / {{ meta.last_page }}
      </span>
      <button class="rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-40" :disabled="meta.current_page >= meta.last_page || loading" @click="goTo(meta.current_page + 1)">
        Proxima
      </button>
    </div>
  </div>
</template>
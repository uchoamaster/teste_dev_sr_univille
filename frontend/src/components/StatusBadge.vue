<script setup>
const props = defineProps({
  status: {
    type: String,
    required: true,
  },
})

const classes = {
  pending: 'border-amber-300 bg-amber-100 text-amber-900',
  processed: 'border-emerald-300 bg-emerald-100 text-emerald-900',
  failed: 'border-rose-300 bg-rose-100 text-rose-900',
  invalid: 'border-slate-300 bg-slate-200 text-slate-700',
}
</script>

<template>
  <span :class="['inline-flex rounded-full border px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em]', classes[props.status] || classes.invalid]">
    {{ props.status }}
  </span>
</template>
<script setup>
defineProps({
  label: {
    type: String,
    required: true,
  },
  value: {
    type: [Number, String],
    required: true,
  },
  hint: {
    type: String,
    default: '',
  },
  tone: {
    type: String,
    default: 'slate',
  },
})

const toneClasses = {
  slate: 'border-slate-200 bg-slate-50 text-slate-900',
  teal: 'border-teal-200 bg-teal-50 text-teal-950',
  amber: 'border-amber-200 bg-amber-50 text-amber-950',
  rose: 'border-rose-200 bg-rose-50 text-rose-950',
}
</script>

<template>
  <article :class="['panel-enter rounded-[20px] border p-4 shadow-sm shadow-slate-200/70', toneClasses[tone] || toneClasses.slate]">
    <p class="text-[11px] font-semibold uppercase tracking-[0.24em] opacity-70">{{ label }}</p>
    <p class="mt-3 break-words text-2xl font-semibold tracking-[-0.04em] sm:text-3xl">{{ value }}</p>
    <p class="mt-2 text-sm opacity-80">{{ hint }}</p>
  </article>
</template>
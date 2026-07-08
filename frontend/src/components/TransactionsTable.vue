<script setup>
import StatusBadge from './StatusBadge.vue'

defineProps({
  transactions: {
    type: Array,
    required: true,
  },
  loading: {
    type: Boolean,
    default: false,
  },
})

function formatMoney(amount, currency = 'BRL') {
  if (amount === null || amount === undefined) {
    return '--'
  }

  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency,
  }).format(Number(amount))
}

function formatDate(value) {
  if (!value) {
    return '--'
  }

  return new Intl.DateTimeFormat('pt-BR', {
    dateStyle: 'short',
    timeStyle: 'short',
  }).format(new Date(value))
}
</script>

<template>
  <section class="slide-up overflow-hidden rounded-[24px] border border-slate-200 bg-white shadow-sm shadow-slate-200/70">
    <div class="border-b border-slate-200 px-4 py-4 sm:px-5">
      <h2 class="text-lg font-semibold tracking-[-0.03em] text-slate-950 sm:text-xl">Transacoes processadas</h2>
      <p class="mt-1 text-sm text-slate-500">Cada item importado e processado de forma independente pela fila.</p>
    </div>

    <div class="grid gap-3 p-4 md:hidden">
      <article v-if="loading" class="rounded-[20px] border border-slate-200 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">
        Carregando transacoes...
      </article>

      <article v-else-if="transactions.length === 0" class="rounded-[20px] border border-slate-200 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">
        Nenhuma transacao encontrada para os filtros atuais.
      </article>

      <article v-for="transaction in transactions" v-else :key="transaction.id" class="rounded-[20px] border border-slate-200 bg-slate-50 p-4">
        <div class="flex items-start justify-between gap-3">
          <div class="min-w-0">
            <p class="text-sm font-semibold text-slate-950">{{ transaction.reference || transaction.external_id || 'Sem referencia' }}</p>
            <p class="mono mt-1 break-all text-xs text-slate-500">{{ transaction.external_id || 'payload invalido' }}</p>
          </div>
          <StatusBadge :status="transaction.status || 'invalid'" />
        </div>

        <dl class="mt-4 grid grid-cols-2 gap-3 text-sm">
          <div class="min-w-0">
            <dt class="text-xs uppercase tracking-[0.18em] text-slate-400">Descricao</dt>
            <dd class="mt-1 text-slate-700">{{ transaction.description || 'Sem descricao' }}</dd>
          </div>
          <div>
            <dt class="text-xs uppercase tracking-[0.18em] text-slate-400">Valor</dt>
            <dd class="mt-1 font-semibold text-slate-950">{{ formatMoney(transaction.amount, transaction.currency || 'BRL') }}</dd>
          </div>
          <div>
            <dt class="text-xs uppercase tracking-[0.18em] text-slate-400">Ocorrencia</dt>
            <dd class="mt-1 text-slate-700">{{ formatDate(transaction.occurred_at) }}</dd>
          </div>
          <div>
            <dt class="text-xs uppercase tracking-[0.18em] text-slate-400">Lote</dt>
            <dd class="mt-1 text-slate-700">#{{ transaction.import_batch_id }}</dd>
          </div>
        </dl>

        <p v-if="transaction.error_message" class="mt-3 text-xs text-rose-600">
          {{ transaction.error_message }}
        </p>
      </article>
    </div>

    <div class="hidden overflow-x-auto md:block">
      <table class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-950 text-left text-xs font-semibold uppercase tracking-[0.22em] text-slate-300">
          <tr>
            <th class="px-5 py-4 sm:px-6">Referencia</th>
            <th class="px-5 py-4">Descricao</th>
            <th class="px-5 py-4">Valor</th>
            <th class="px-5 py-4">Status</th>
            <th class="px-5 py-4">Ocorrencia</th>
            <th class="px-5 py-4">Lote</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-slate-100 bg-white text-sm text-slate-700">
          <tr v-if="loading">
            <td colspan="6" class="px-6 py-12 text-center text-slate-500">Carregando transacoes...</td>
          </tr>

          <tr v-else-if="transactions.length === 0">
            <td colspan="6" class="px-6 py-12 text-center text-slate-500">Nenhuma transacao encontrada para os filtros atuais.</td>
          </tr>

          <tr v-for="transaction in transactions" :key="transaction.id" class="align-top transition hover:bg-slate-50/80">
            <td class="px-5 py-4 sm:px-6">
              <div class="font-semibold text-slate-950">{{ transaction.reference || transaction.external_id || 'Sem referencia' }}</div>
              <div class="mono mt-1 text-xs text-slate-500">{{ transaction.external_id || 'payload invalido' }}</div>
            </td>
            <td class="px-5 py-4">
              <div>{{ transaction.description || 'Sem descricao' }}</div>
              <div v-if="transaction.error_message" class="mt-2 max-w-xs text-xs text-rose-600">
                {{ transaction.error_message }}
              </div>
            </td>
            <td class="px-5 py-4 font-semibold text-slate-950">
              {{ formatMoney(transaction.amount, transaction.currency || 'BRL') }}
            </td>
            <td class="px-5 py-4">
              <StatusBadge :status="transaction.status || 'invalid'" />
            </td>
            <td class="px-5 py-4 text-slate-500">
              {{ formatDate(transaction.occurred_at) }}
            </td>
            <td class="px-5 py-4">
              <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                #{{ transaction.import_batch_id }}
              </span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>
</template>
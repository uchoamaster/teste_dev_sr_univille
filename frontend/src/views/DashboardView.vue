<script setup>
import { computed, onMounted, onUnmounted } from 'vue'
import FilterPanel from '../components/FilterPanel.vue'
import MetricCard from '../components/MetricCard.vue'
import PaginationBar from '../components/PaginationBar.vue'
import TransactionsTable from '../components/TransactionsTable.vue'
import { ApiError } from '../lib/api'
import { useDashboardStore } from '../stores/dashboardStore'
import { useSessionStore } from '../stores/sessionStore'

const dashboard = useDashboardStore()
const session = useSessionStore()

const lastImportLabel = computed(() => {
  if (!dashboard.state.summary.last_import_at) {
    return 'Nenhuma importacao executada ainda.'
  }

  return new Intl.DateTimeFormat('pt-BR', {
    dateStyle: 'short',
    timeStyle: 'short',
  }).format(new Date(dashboard.state.summary.last_import_at))
})

async function bootstrap() {
  try {
    await dashboard.initialize()
    dashboard.startPolling(() => session.clearSession())
  } catch (error) {
    if (error instanceof ApiError && error.status === 401) {
      session.clearSession()
    }
  }
}

async function runImport() {
  try {
    await dashboard.importTransactions()
  } catch (error) {
    if (error instanceof ApiError && error.status === 401) {
      session.clearSession()
    }
  }
}

async function applyFilters(filters) {
  try {
    await dashboard.applyFilters(filters)
  } catch (error) {
    if (error instanceof ApiError && error.status === 401) {
      session.clearSession()
    }
  }
}

async function resetFilters() {
  try {
    await dashboard.resetFilters()
  } catch (error) {
    if (error instanceof ApiError && error.status === 401) {
      session.clearSession()
    }
  }
}

async function changePage(page) {
  try {
    await dashboard.refresh(page)
  } catch (error) {
    if (error instanceof ApiError && error.status === 401) {
      session.clearSession()
    }
  }
}

onMounted(() => {
  void bootstrap()
})

onUnmounted(() => {
  dashboard.stopPolling()
})
</script>

<template>
  <section class="flex flex-col gap-5">
    <div class="grid gap-4 xl:grid-cols-[minmax(0,1.5fr)_minmax(320px,0.9fr)]">
      <article class="panel-enter rounded-[24px] border border-slate-200 bg-white p-5 shadow-sm shadow-slate-200/70 sm:p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
          <div>
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-teal-700">Sessao autenticada</p>
            <h2 class="mt-2 text-2xl font-semibold tracking-[-0.04em] text-slate-950 sm:text-3xl">{{ session.state.user?.name }}</h2>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
              Painel conectado a API protegida por token, com atualizacao automatica e importacao manual.
            </p>
          </div>

          <div class="flex flex-col gap-2 sm:flex-row lg:shrink-0">
            <button class="rounded-2xl bg-slate-950 px-4 py-3 text-sm font-semibold text-white transition hover:bg-teal-700 disabled:cursor-not-allowed disabled:bg-slate-400" :disabled="dashboard.state.isImporting" @click="runImport">
              {{ dashboard.state.isImporting ? 'Importando...' : 'Importar transacoes' }}
            </button>
            <button class="rounded-2xl border border-slate-300 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100" @click="session.logout()">
              Sair
            </button>
          </div>
        </div>

        <div class="mt-5 rounded-[20px] border border-slate-200 bg-slate-50 px-4 py-4 text-sm text-slate-700 sm:px-5">
          <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
            <span class="font-medium text-slate-900">Ultima importacao consolidada</span>
            <span class="mono text-xs uppercase tracking-[0.18em] text-slate-500">{{ lastImportLabel }}</span>
          </div>
        </div>
      </article>

      <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-1">
        <MetricCard label="Pendentes" :value="dashboard.state.summary.pending_count" hint="Aguardando fila." tone="amber" />
        <MetricCard label="Processadas" :value="dashboard.state.summary.processed_count" hint="Concluidas com sucesso." tone="teal" />
      </div>
    </div>

    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
      <MetricCard label="Falhas" :value="dashboard.state.summary.failed_count" hint="Retries esgotados." tone="rose" />
      <MetricCard label="Invalidas" :value="dashboard.state.summary.invalid_count" hint="Payloads invalidos." tone="slate" />
      <MetricCard label="Total" :value="dashboard.state.summary.total_transactions" hint="Itens importados." tone="slate" />
      <MetricCard label="Usuario" :value="session.state.user?.email || '--'" hint="Sessao ativa." tone="teal" />
    </div>

    <p v-if="dashboard.state.error" class="rounded-[20px] border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
      {{ dashboard.state.error }}
    </p>

    <FilterPanel :filters="dashboard.state.filters" :loading="dashboard.state.isLoading" @apply="applyFilters" @reset="resetFilters" />
    <TransactionsTable :transactions="dashboard.state.transactions" :loading="dashboard.state.isLoading" />
    <PaginationBar :meta="dashboard.state.meta" :loading="dashboard.state.isLoading" @change="changePage" />
  </section>
</template>
import { reactive } from 'vue'
import { ApiError, apiRequest, formatApiError } from '../lib/api'

const defaultSummary = () => ({
  pending_count: 0,
  processed_count: 0,
  failed_count: 0,
  invalid_count: 0,
  total_transactions: 0,
  last_import_at: null,
})

const defaultFilters = () => ({
  status: '',
  start_date: '',
  end_date: '',
  min_amount: '',
  max_amount: '',
})

const state = reactive({
  summary: defaultSummary(),
  transactions: [],
  meta: {
    current_page: 1,
    last_page: 1,
    per_page: 15,
    total: 0,
    from: null,
    to: null,
  },
  filters: defaultFilters(),
  isLoading: false,
  isImporting: false,
  error: '',
  pollerId: null,
})

function normalizePagination(meta = {}) {
  return {
    current_page: meta.current_page || 1,
    last_page: meta.last_page || 1,
    per_page: meta.per_page || 15,
    total: meta.total || 0,
    from: meta.from ?? null,
    to: meta.to ?? null,
  }
}

async function refresh(page = state.meta.current_page, silent = false) {
  if (!silent) {
    state.isLoading = true
  }

  state.error = ''

  try {
    const [summary, transactions] = await Promise.all([
      apiRequest('/dashboard/summary'),
      apiRequest('/transactions', {
        query: {
          page,
          per_page: state.meta.per_page,
          ...state.filters,
        },
      }),
    ])

    state.summary = summary
    state.transactions = transactions.data || []
    state.meta = normalizePagination(transactions.meta)
  } catch (error) {
    state.error = formatApiError(error)
    throw error
  } finally {
    if (!silent) {
      state.isLoading = false
    }
  }
}

export function useDashboardStore() {
  return {
    state,
    async initialize() {
      await refresh(1)
    },
    async refresh(page = state.meta.current_page, silent = false) {
      await refresh(page, silent)
    },
    async importTransactions() {
      state.isImporting = true
      state.error = ''

      try {
        await apiRequest('/transactions/import', {
          method: 'POST',
          body: { source: 'mock' },
        })

        await refresh(1)
      } catch (error) {
        state.error = formatApiError(error)
        throw error
      } finally {
        state.isImporting = false
      }
    },
    async applyFilters(nextFilters) {
      state.filters = {
        ...state.filters,
        ...nextFilters,
      }

      await refresh(1)
    },
    async resetFilters() {
      state.filters = defaultFilters()
      await refresh(1)
    },
    startPolling(onUnauthorized) {
      this.stopPolling()

      state.pollerId = window.setInterval(async () => {
        try {
          await refresh(state.meta.current_page, true)
        } catch (error) {
          if (error instanceof ApiError && error.status === 401) {
            this.stopPolling()
            onUnauthorized?.()
          }
        }
      }, 15000)
    },
    stopPolling() {
      if (state.pollerId !== null) {
        window.clearInterval(state.pollerId)
        state.pollerId = null
      }
    },
  }
}
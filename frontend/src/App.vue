<script setup>
import { onMounted } from 'vue'
import AuthView from './views/AuthView.vue'
import DashboardView from './views/DashboardView.vue'
import { useSessionStore } from './stores/sessionStore'

const session = useSessionStore()

onMounted(() => {
  session.restore()
})
</script>

<template>
  <div class="app-shell min-h-screen bg-[linear-gradient(180deg,_#f8fafc_0%,_#f8fafc_22%,_#eef2f7_100%)] text-slate-900">
    <div class="pointer-events-none absolute inset-0 opacity-30">
      <div class="absolute inset-x-0 top-0 h-72 bg-[radial-gradient(circle_at_top,_rgba(13,148,136,0.18),_transparent_58%)]"></div>
      <div class="grain absolute inset-0"></div>
    </div>

    <main class="relative mx-auto flex min-h-screen w-full max-w-7xl flex-col px-4 py-5 sm:px-6 lg:px-8 lg:py-8">
      <header class="mb-6 flex flex-col gap-4 rounded-[24px] border border-slate-200 bg-white/88 px-5 py-5 shadow-sm shadow-slate-200/60 backdrop-blur md:flex-row md:items-end md:justify-between md:px-7">
        <div>
          <span class="inline-flex rounded-full border border-teal-200 bg-teal-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.28em] text-teal-700">
            Univille Bank
          </span>
          <h1 class="mt-3 max-w-2xl text-2xl font-semibold tracking-[-0.04em] text-slate-950 sm:text-4xl">
            Painel de conciliacao financeira.
          </h1>
        </div>

        <p class="max-w-xl text-sm leading-6 text-slate-500 sm:text-base">
          Consulte transacoes, acompanhe a fila e dispare novas importacoes de forma direta.
        </p>
      </header>

      <AuthView v-if="!session.isAuthenticated.value" />
      <DashboardView v-else />
    </main>
  </div>
</template>

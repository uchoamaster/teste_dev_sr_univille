<script setup>
import { reactive, ref } from 'vue'
import { useSessionStore } from '../stores/sessionStore'

const session = useSessionStore()
const mode = ref('login')
const form = reactive({
  name: '',
  email: '',
  password: '',
})

async function submit() {
  if (mode.value === 'login') {
    await session.login({
      email: form.email,
      password: form.password,
    })

    return
  }

  await session.register({
    name: form.name,
    email: form.email,
    password: form.password,
  })
}
</script>

<template>
  <section class="panel-enter mx-auto grid w-full max-w-6xl gap-6 lg:grid-cols-[1.1fr_0.9fr]">
    <article class="rounded-[32px] border border-white/15 bg-slate-950/85 p-8 text-white shadow-2xl shadow-slate-950/30 backdrop-blur">
      <p class="inline-flex rounded-full bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.26em] text-teal-200">
        Acesso protegido
      </p>
      <h2 class="mt-5 text-3xl font-bold tracking-[-0.05em] sm:text-4xl">Entre para operar o dashboard.</h2>
      <p class="mt-4 max-w-xl text-base leading-7 text-slate-300">
        O frontend armazena o token da API e o envia automaticamente nas requisicoes protegidas do painel de conciliacao.
      </p>

      <div class="mt-10 grid gap-4 sm:grid-cols-3">
        <div class="rounded-[24px] border border-white/10 bg-white/5 p-4">
          <p class="text-xs uppercase tracking-[0.22em] text-slate-400">Stack</p>
          <p class="mt-3 text-lg font-semibold">Vue 3 + Vite</p>
        </div>
        <div class="rounded-[24px] border border-white/10 bg-white/5 p-4">
          <p class="text-xs uppercase tracking-[0.22em] text-slate-400">Seguranca</p>
          <p class="mt-3 text-lg font-semibold">Bearer Token</p>
        </div>
        <div class="rounded-[24px] border border-white/10 bg-white/5 p-4">
          <p class="text-xs uppercase tracking-[0.22em] text-slate-400">Fila</p>
          <p class="mt-3 text-lg font-semibold">Processamento Async</p>
        </div>
      </div>
    </article>

    <article class="rounded-[32px] border border-white/40 bg-white/85 p-8 shadow-xl shadow-slate-900/10 backdrop-blur">
      <div class="flex rounded-full bg-slate-100 p-1 text-sm font-semibold text-slate-600">
        <button class="flex-1 rounded-full px-4 py-2 transition" :class="mode === 'login' ? 'bg-slate-950 text-white' : ''" @click="mode = 'login'">
          Login
        </button>
        <button class="flex-1 rounded-full px-4 py-2 transition" :class="mode === 'register' ? 'bg-slate-950 text-white' : ''" @click="mode = 'register'">
          Cadastro
        </button>
      </div>

      <form class="mt-8 space-y-4" @submit.prevent="submit">
        <label v-if="mode === 'register'" class="block text-sm font-medium text-slate-600">
          Nome
          <input v-model="form.name" type="text" required class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none transition focus:border-teal-500" placeholder="Seu nome" />
        </label>

        <label class="block text-sm font-medium text-slate-600">
          E-mail
          <input v-model="form.email" type="email" required class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none transition focus:border-teal-500" placeholder="voce@empresa.com" />
        </label>

        <label class="block text-sm font-medium text-slate-600">
          Senha
          <input v-model="form.password" type="password" required minlength="8" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none transition focus:border-teal-500" placeholder="Minimo 8 caracteres" />
        </label>

        <p v-if="session.state.error" class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
          {{ session.state.error }}
        </p>

        <button type="submit" :disabled="session.state.isSubmitting" class="w-full rounded-full bg-teal-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-teal-700 disabled:cursor-not-allowed disabled:bg-teal-300">
          {{ session.state.isSubmitting ? 'Enviando...' : mode === 'login' ? 'Entrar' : 'Criar conta' }}
        </button>
      </form>
    </article>
  </section>
</template>
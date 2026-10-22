<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { api, setSession } from '../api'
const router = useRouter()
const form = ref({ email: '', password: '' })
const error = ref('')
const errors = computed(() => ({
  email: /^\S+@\S+\.\S+$/.test(form.value.email) ? '' : 'Enter a valid email address',
  password: form.value.password ? '' : 'Enter your password'
}))
const valid = computed(() => !errors.value.email && !errors.value.password)
async function submit() {
  try {
    const r = await api('login', { method: 'POST', body: form.value })
    setSession(r.user, r.token)
    router.push('/dashboard')
  } catch (e) { error.value = e.message }
}
</script>

<template>
  <div class="mx-auto" style="max-width: 420px">
    <h1 class="h3">Log in</h1>
    <div v-if="error" class="alert alert-danger">{{ error }}</div>
    <form @submit.prevent="submit" novalidate>
      <div class="mb-3">
        <label class="form-label" for="email">Email</label>
        <input id="email" v-model="form.email" type="email" class="form-control" :class="{ 'is-invalid': form.email && errors.email }" />
        <div class="invalid-feedback">{{ errors.email }}</div>
      </div>
      <div class="mb-3">
        <label class="form-label" for="pw">Password</label>
        <input id="pw" v-model="form.password" type="password" class="form-control" />
      </div>
      <button class="btn btn-accent w-100" :disabled="!valid">Log in</button>
    </form>
  </div>
</template>

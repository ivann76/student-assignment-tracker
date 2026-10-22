<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { api, setSession } from '../api'
const router = useRouter()
const form = ref({ name: '', email: '', password: '', confirm: '' })
const error = ref('')
const errors = computed(() => ({
  name: form.value.name.trim() ? '' : 'Enter your name',
  email: /^\S+@\S+\.\S+$/.test(form.value.email) ? '' : 'Enter a valid email address',
  password: /^(?=.*\d)(?=.*[A-Za-z]).{8,}$/.test(form.value.password) ? '' : 'Use 8+ characters with letters and numbers',
  confirm: form.value.confirm === form.value.password ? '' : 'Passwords do not match'
}))
const valid = computed(() => Object.values(errors.value).every((e) => !e))
async function submit() {
  try {
    const r = await api('register', { method: 'POST', body: form.value })
    setSession(r.user, r.token)
    router.push('/dashboard')
  } catch (e) { error.value = e.message }
}
</script>

<template>
  <div class="mx-auto" style="max-width: 420px">
    <h1 class="h3">Create account</h1>
    <div v-if="error" class="alert alert-danger">{{ error }}</div>
    <form @submit.prevent="submit" novalidate>
      <div v-for="f in ['name', 'email', 'password', 'confirm']" :key="f" class="mb-3">
        <label class="form-label text-capitalize" :for="f">{{ f === 'confirm' ? 'Confirm password' : f }}</label>
        <input :id="f" v-model="form[f]" :type="f === 'email' ? 'email' : f === 'name' ? 'text' : 'password'"
          class="form-control" :class="{ 'is-invalid': form[f] && errors[f] }" />
        <div class="invalid-feedback">{{ errors[f] }}</div>
      </div>
      <button class="btn btn-accent w-100" :disabled="!valid">Register</button>
    </form>
  </div>
</template>

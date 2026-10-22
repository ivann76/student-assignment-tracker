<script setup>
import { useRouter } from 'vue-router'
import { auth, setSession } from './api'
import NavBar from './components/NavBar.vue'
const router = useRouter()
function logout() { setSession(null, ''); router.push('/') }
</script>

<template>
  <NavBar :user="auth.user" @logout="logout" />
  <main class="container py-4"><RouterView /></main>
</template>

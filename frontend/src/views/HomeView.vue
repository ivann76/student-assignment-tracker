<script setup>
import { ref, onMounted } from 'vue'
import { api, auth } from '../api'
import AssignmentCard from '../components/AssignmentCard.vue'
const preview = ref([])
onMounted(async () => { try { preview.value = await api('assignments') } catch { /* preview is optional */ } })
</script>

<template>
  <div class="text-center py-4">
    <h1>Know what is due, before it is due.</h1>
    <p class="lead">Track assignments across your units and share useful ones with classmates.</p>
    <RouterLink v-if="!auth.user" to="/register" class="btn btn-accent btn-lg">Create free account</RouterLink>
  </div>
  <h2 class="h5 mt-4">Shared by students</h2>
  <p v-if="!auth.user" class="text-muted">Log in to see everything and add your own.</p>
  <div class="row g-3">
    <div v-for="a in preview" :key="a.id" class="col-12 col-md-4"><AssignmentCard :item="a" /></div>
  </div>
</template>

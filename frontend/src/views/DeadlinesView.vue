<script setup>
import { ref, computed, onMounted } from 'vue'
import { api, auth } from '../api'
const items = ref([])
onMounted(async () => { items.value = await api('assignments') })
// Group my unfinished assignments by due date
const grouped = computed(() => {
  const g = {}
  items.value.filter((a) => a.user_id == auth.user.id && a.status !== 'done').sort((a, b) => a.due_date.localeCompare(b.due_date))
    .forEach((a) => { (g[a.due_date] ||= []).push(a) })
  return g
})
</script>

<template>
  <h1 class="h3">Deadlines</h1>
  <p v-if="!Object.keys(grouped).length" class="text-muted">No open deadlines.</p>
  <div v-for="(list, date) in grouped" :key="date" class="mb-3">
    <h2 class="h6">{{ date }}</h2>
    <ul class="list-group"><li v-for="a in list" :key="a.id" class="list-group-item"><RouterLink :to="`/assignments/${a.id}`">{{ a.title }}</RouterLink> <small class="text-muted">{{ a.unit_code }}</small></li></ul>
  </div>
</template>

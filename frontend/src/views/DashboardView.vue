<script setup>
import { ref, computed, onMounted } from 'vue'
import { api, auth } from '../api'
import AssignmentCard from '../components/AssignmentCard.vue'
const items = ref([])
onMounted(async () => { items.value = await api('assignments') })
const mine = computed(() => items.value.filter((a) => a.user_id == auth.user.id))
const upcoming = computed(() => mine.value.filter((a) => a.status !== 'done').sort((a, b) => a.due_date.localeCompare(b.due_date)).slice(0, 3))
const doneCount = computed(() => mine.value.filter((a) => a.status === 'done').length)
const progress = computed(() => (mine.value.length ? Math.round((doneCount.value / mine.value.length) * 100) : 0))
</script>

<template>
  <h1 class="h3">Hi {{ auth.user.name }}</h1>
  <p>{{ doneCount }} of {{ mine.length }} assignments done</p>
  <div class="progress mb-4" role="progressbar" :aria-valuenow="progress"><div class="progress-bar bg-success" :style="{ width: progress + '%' }">{{ progress }}%</div></div>
  <h2 class="h5">Next deadlines</h2>
  <p v-if="!upcoming.length" class="text-muted">Nothing due. <RouterLink to="/assignments/new">Add an assignment</RouterLink></p>
  <div class="row g-3"><div v-for="a in upcoming" :key="a.id" class="col-12 col-md-4"><AssignmentCard :item="a" /></div></div>
</template>

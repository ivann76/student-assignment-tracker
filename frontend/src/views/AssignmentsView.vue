<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { api } from '../api'
import AssignmentCard from '../components/AssignmentCard.vue'
import PaginationControl from '../components/PaginationControl.vue'

const items = ref([])
const search = ref('')
const status = ref('')
const sortBy = ref('due_date')
const page = ref(1)
const perPage = 6

onMounted(async () => { items.value = await api('assignments') })

// Filter, then sort, then slice into pages: all computed so the UI updates by itself
const filtered = computed(() => {
  const q = search.value.toLowerCase()
  const order = ['high', 'medium', 'low']
  return items.value
    .filter((a) => (a.title + a.unit_code + a.unit_name).toLowerCase().includes(q))
    .filter((a) => !status.value || a.status === status.value)
    .sort((a, b) => {
      if (sortBy.value === 'title') return a.title.localeCompare(b.title)
      if (sortBy.value === 'likes') return b.likes - a.likes
      if (sortBy.value === 'priority') return order.indexOf(a.priority) - order.indexOf(b.priority)
      return a.due_date.localeCompare(b.due_date)
    })
})
const totalPages = computed(() => Math.max(1, Math.ceil(filtered.value.length / perPage)))
const paged = computed(() => filtered.value.slice((page.value - 1) * perPage, page.value * perPage))
watch([search, status, sortBy], () => { page.value = 1 })
</script>

<template>
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Assignments</h1>
    <RouterLink to="/assignments/new" class="btn btn-accent">Add assignment</RouterLink>
  </div>
  <div class="row g-2 mb-3">
    <div class="col-12 col-md-6"><input v-model="search" class="form-control" placeholder="Search by title or unit" aria-label="Search" /></div>
    <div class="col-6 col-md-3">
      <select v-model="status" class="form-select" aria-label="Filter by status">
        <option value="">All statuses</option><option value="todo">To do</option><option value="doing">In progress</option><option value="done">Done</option>
      </select>
    </div>
    <div class="col-6 col-md-3">
      <select v-model="sortBy" class="form-select" aria-label="Sort by">
        <option value="due_date">Due date</option><option value="title">Title</option><option value="priority">Priority</option><option value="likes">Most liked</option>
      </select>
    </div>
  </div>
  <p v-if="!paged.length" class="text-muted">No assignments match. Try clearing the filters or add a new one.</p>
  <div class="row g-3">
    <div v-for="a in paged" :key="a.id" class="col-12 col-md-6 col-lg-4"><AssignmentCard :item="a" /></div>
  </div>
  <PaginationControl class="mt-4" :page="page" :total-pages="totalPages" @change="page = $event" />
</template>

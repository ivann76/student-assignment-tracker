<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { api } from '../api'
const route = useRoute()
const router = useRouter()
const editing = computed(() => !!route.params.id)
const units = ref([])
const error = ref('')
const form = ref({ title: '', description: '', unit_id: '', due_date: '', status: 'todo', priority: 'medium', is_shared: false })
const today = new Date().toISOString().slice(0, 10)

const errors = computed(() => ({
  title: form.value.title.trim() ? '' : 'Title is required',
  unit_id: form.value.unit_id ? '' : 'Choose a unit',
  due_date: !form.value.due_date ? 'Pick a due date' : (!editing.value && form.value.due_date < today ? 'Due date cannot be in the past' : '')
}))
const valid = computed(() => Object.values(errors.value).every((e) => !e))

onMounted(async () => {
  units.value = await api('units')
  if (editing.value) {
    const a = await api('assignments', { id: route.params.id })
    form.value = { ...a, is_shared: !!+a.is_shared }
  }
})
async function save() {
  try {
    if (editing.value) await api('assignments', { method: 'PUT', id: route.params.id, body: form.value })
    else await api('assignments', { method: 'POST', body: form.value })
    router.push('/assignments')
  } catch (e) { error.value = e.message }
}
</script>

<template>
  <div class="mx-auto" style="max-width: 560px">
    <h1 class="h3">{{ editing ? 'Edit assignment' : 'New assignment' }}</h1>
    <div v-if="error" class="alert alert-danger">{{ error }}</div>
    <form @submit.prevent="save" novalidate>
      <div class="mb-3">
        <label class="form-label" for="t">Title</label>
        <input id="t" v-model="form.title" class="form-control" :class="{ 'is-invalid': form.title !== '' && errors.title }" />
        <div class="invalid-feedback">{{ errors.title }}</div>
      </div>
      <div class="mb-3">
        <label class="form-label" for="d">Description</label>
        <textarea id="d" v-model="form.description" rows="3" class="form-control"></textarea>
      </div>
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label" for="u">Unit</label>
          <select id="u" v-model="form.unit_id" class="form-select">
            <option value="" disabled>Select unit</option>
            <option v-for="u in units" :key="u.id" :value="u.id">{{ u.code }} {{ u.name }}</option>
          </select>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label" for="due">Due date</label>
          <input id="due" v-model="form.due_date" type="date" class="form-control" :class="{ 'is-invalid': form.due_date && errors.due_date }" />
          <div class="invalid-feedback">{{ errors.due_date }}</div>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label" for="s">Status</label>
          <select id="s" v-model="form.status" class="form-select"><option value="todo">To do</option><option value="doing">In progress</option><option value="done">Done</option></select>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label" for="p">Priority</label>
          <select id="p" v-model="form.priority" class="form-select"><option value="low">Low</option><option value="medium">Medium</option><option value="high">High</option></select>
        </div>
      </div>
      <div class="form-check mb-3">
        <input id="sh" v-model="form.is_shared" type="checkbox" class="form-check-input" />
        <label class="form-check-label" for="sh">Share with other students</label>
      </div>
      <button class="btn btn-accent" :disabled="!valid">Save assignment</button>
      <RouterLink to="/assignments" class="btn btn-link">Cancel</RouterLink>
    </form>
  </div>
</template>

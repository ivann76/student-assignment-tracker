<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { api, auth } from '../api'
import StatusBadge from '../components/StatusBadge.vue'
const route = useRoute()
const router = useRouter()
const item = ref(null)
const comments = ref([])
const body = ref('')
const canEdit = computed(() => item.value && (item.value.user_id == auth.user.id || auth.user.role === 'admin'))

async function load() {
  item.value = await api('assignments', { id: route.params.id })
  comments.value = await api('comments', { id: route.params.id })
}
onMounted(load)
async function toggleLike() { await api('like', { method: 'POST', id: item.value.id }); await load() }
async function addComment() {
  if (!body.value.trim()) return
  await api('comments', { method: 'POST', id: item.value.id, body: { body: body.value } })
  body.value = ''
  await load()
}
async function remove() {
  if (!confirm('Delete this assignment?')) return
  await api('assignments', { method: 'DELETE', id: item.value.id })
  router.push('/assignments')
}
</script>

<template>
  <div v-if="item" class="mx-auto" style="max-width: 720px">
    <small class="text-muted">{{ item.unit_code }} {{ item.unit_name }} | by {{ item.owner }}</small>
    <h1 class="h3 mt-1">{{ item.title }} <StatusBadge :status="item.status" /></h1>
    <p>{{ item.description || 'No description.' }}</p>
    <p>Due <strong>{{ item.due_date }}</strong> | Priority {{ item.priority }}</p>
    <div class="mb-4">
      <button class="btn btn-sm me-2" :class="+item.liked ? 'btn-accent' : 'btn-outline-secondary'" @click="toggleLike">
        {{ +item.liked ? 'Liked' : 'Like' }} ({{ item.likes }})
      </button>
      <template v-if="canEdit">
        <RouterLink :to="`/assignments/${item.id}/edit`" class="btn btn-sm btn-outline-primary me-2">Edit</RouterLink>
        <button class="btn btn-sm btn-outline-danger" @click="remove">Delete</button>
      </template>
    </div>
    <h2 class="h5">Comments</h2>
    <form class="d-flex gap-2 mb-3" @submit.prevent="addComment">
      <input v-model="body" class="form-control" maxlength="500" placeholder="Add a comment" aria-label="Comment" />
      <button class="btn btn-accent">Post</button>
    </form>
    <p v-if="!comments.length" class="text-muted">No comments yet. Start the conversation.</p>
    <div v-for="c in comments" :key="c.id" class="border-bottom py-2"><strong>{{ c.name }}</strong> <small class="text-muted">{{ c.created_at }}</small><br />{{ c.body }}</div>
  </div>
</template>

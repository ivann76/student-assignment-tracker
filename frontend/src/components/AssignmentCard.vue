<script setup>
import { computed } from 'vue'
import StatusBadge from './StatusBadge.vue'
const props = defineProps({ item: Object })
const daysLeft = computed(() => Math.ceil((new Date(props.item.due_date) - new Date()) / 86400000))
</script>

<template>
  <div class="card h-100" :class="{ 'due-soon': daysLeft <= 2 && item.status !== 'done' }">
    <div class="card-body">
      <div class="d-flex justify-content-between">
        <small class="text-muted">{{ item.unit_code }}</small>
        <StatusBadge :status="item.status" />
      </div>
      <h5 class="card-title mt-2">
        <RouterLink :to="`/assignments/${item.id}`" class="text-decoration-none">{{ item.title }}</RouterLink>
      </h5>
      <p class="mb-1">Due {{ item.due_date }} ({{ daysLeft < 0 ? 'overdue' : daysLeft + ' days left' }})</p>
      <small class="text-muted">Priority: {{ item.priority }} | {{ item.likes }} likes</small>
    </div>
  </div>
</template>

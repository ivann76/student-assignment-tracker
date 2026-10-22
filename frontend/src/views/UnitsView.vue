<script setup>
import { ref, onMounted } from 'vue'
import { api } from '../api'
const units = ref([])
onMounted(async () => { units.value = await api('units') })
</script>

<template>
  <h1 class="h3">Units</h1>
  <ul class="list-group"><li v-for="u in units" :key="u.id" class="list-group-item"><strong>{{ u.code }}</strong> {{ u.name }}</li></ul>
</template>

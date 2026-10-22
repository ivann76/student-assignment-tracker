<script setup>
defineProps({ page: Number, totalPages: Number })
defineEmits(['change'])
</script>

<template>
  <nav v-if="totalPages > 1" aria-label="Pagination">
    <ul class="pagination justify-content-center">
      <li class="page-item" :class="{ disabled: page === 1 }">
        <button class="page-link" @click="$emit('change', page - 1)">Previous</button>
      </li>
      <li v-for="n in totalPages" :key="n" class="page-item" :class="{ active: n === page }">
        <button class="page-link" @click="$emit('change', n)">{{ n }}</button>
      </li>
      <li class="page-item" :class="{ disabled: page === totalPages }">
        <button class="page-link" @click="$emit('change', page + 1)">Next</button>
      </li>
    </ul>
  </nav>
</template>

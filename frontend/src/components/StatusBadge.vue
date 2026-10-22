<script setup>
import { computed } from 'vue'
const props = defineProps({ status: String })
const cls = computed(() => ({ todo: 'bg-secondary', doing: 'bg-warning text-dark', done: 'bg-success' }[props.status]))
const label = computed(() => ({ todo: 'To do', doing: 'In progress', done: 'Done' }[props.status]))
</script>
<template><span class="badge" :class="cls">{{ label }}</span></template>

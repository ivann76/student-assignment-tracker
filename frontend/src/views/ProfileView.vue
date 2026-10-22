<script setup>
import { auth } from '../api'
</script>

<template>
  <h1 class="h3">Profile</h1>
  <dl class="row"><dt class="col-4">Name</dt><dd class="col-8">{{ auth.user.name }}</dd><dt class="col-4">Email</dt><dd class="col-8">{{ auth.user.email }}</dd><dt class="col-4">Role</dt><dd class="col-8">{{ auth.user.role }}</dd></dl>
</template>

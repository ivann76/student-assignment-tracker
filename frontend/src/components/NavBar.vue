<script setup>
defineProps({ user: Object })
defineEmits(['logout'])
</script>

<template>
  <nav class="navbar navbar-expand-md bg-white border-bottom">
    <div class="container">
      <RouterLink class="navbar-brand" to="/">Assignment Tracker</RouterLink>
      <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#menu" aria-label="Toggle menu">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div id="menu" class="collapse navbar-collapse">
        <ul v-if="user" class="navbar-nav me-auto">
          <li class="nav-item"><RouterLink class="nav-link" to="/dashboard">Dashboard</RouterLink></li>
          <li class="nav-item"><RouterLink class="nav-link" to="/assignments">Assignments</RouterLink></li>
          <li class="nav-item"><RouterLink class="nav-link" to="/deadlines">Deadlines</RouterLink></li>
          <li class="nav-item"><RouterLink class="nav-link" to="/units">Units</RouterLink></li>
        </ul>
        <ul class="navbar-nav ms-auto">
          <template v-if="user">
            <li class="nav-item"><RouterLink class="nav-link" to="/profile">{{ user.name }}</RouterLink></li>
            <li class="nav-item"><button class="btn btn-link nav-link" @click="$emit('logout')">Log out</button></li>
          </template>
          <template v-else>
            <li class="nav-item"><RouterLink class="nav-link" to="/login">Log in</RouterLink></li>
            <li class="nav-item"><RouterLink class="nav-link" to="/register">Register</RouterLink></li>
          </template>
        </ul>
      </div>
    </div>
  </nav>
</template>

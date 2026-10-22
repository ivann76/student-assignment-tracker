<script setup>
import {
  LayoutDashboard,
  ClipboardList,
  CalendarDays,
  BookOpen,
  Settings,
  LogOut
} from 'lucide-vue-next'
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const props = defineProps({
  user: Object
})

const emit = defineEmits(['logout'])
const route = useRoute()

const initials = computed(() =>
  (props.user?.name || 'User')
    .split(' ')
    .filter(Boolean)
    .map(word => word[0])
    .slice(0, 2)
    .join('')
    .toUpperCase()
)

const menuItems = [
  { name: 'Dashboard', path: '/dashboard', icon: LayoutDashboard },
  { name: 'Assignments', path: '/assignments', icon: ClipboardList },
  { name: 'Deadlines', path: '/deadlines', icon: CalendarDays },
  { name: 'Units', path: '/units', icon: BookOpen }
]
</script>

<template>
  <aside class="app-sidebar">
    <!-- Account -->
    <div class="sidebar-account">
      <div class="avatar">{{ initials }}</div>
      <div class="account-details">
        <strong>{{ user?.name || 'Guest' }}</strong>
        <small>Assignment Tracker</small>
      </div>
    </div>

    <!-- Navigation -->
    <div class="sidebar-section">
      <p class="section-label">GENERAL</p>

      <RouterLink
        v-for="item in menuItems"
        v-if="user"
        :key="item.path"
        :to="item.path"
        class="sidebar-link"
        :class="{ active: route.path === item.path || route.path.startsWith(item.path + '/') }"
      >
      <component :is="item.icon" :size="20" :stroke-width="1.8" />
        {{ item.name }}
      </RouterLink>

      <template v-if="!user">
        <RouterLink to="/login" class="sidebar-link">
          Log in
        </RouterLink>
        <RouterLink to="/register" class="sidebar-link">
          Register
        </RouterLink>
      </template>
    </div>

   <!-- Bottom -->
<div v-if="user" class="sidebar-bottom">

  <RouterLink to="/profile" class="sidebar-link">
    <Settings :size="20" :stroke-width="1.8" />
    <span>Settings</span>
  </RouterLink>

  <button
    class="sidebar-link logout"
    type="button"
    @click="emit('logout')"
  >
    <LogOut :size="20" :stroke-width="1.8" />
    <span>Log out</span>
  </button>

</div>
  </aside>
</template>
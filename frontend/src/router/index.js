import { createRouter, createWebHashHistory } from 'vue-router'
import { auth } from '../api'

const routes = [
  { path: '/', name: 'home', component: () => import('../views/HomeView.vue') },
  { path: '/login', name: 'login', component: () => import('../views/LoginView.vue') },
  { path: '/register', name: 'register', component: () => import('../views/RegisterView.vue') },
  { path: '/dashboard', name: 'dashboard', component: () => import('../views/DashboardView.vue'), meta: { auth: true } },
  { path: '/assignments', name: 'assignments', component: () => import('../views/AssignmentsView.vue'), meta: { auth: true } },
  { path: '/assignments/new', name: 'new', component: () => import('../views/AssignmentFormView.vue'), meta: { auth: true } },
  { path: '/assignments/:id', name: 'detail', component: () => import('../views/AssignmentDetailView.vue'), meta: { auth: true } },
  { path: '/assignments/:id/edit', name: 'edit', component: () => import('../views/AssignmentFormView.vue'), meta: { auth: true } },
  { path: '/deadlines', name: 'deadlines', component: () => import('../views/DeadlinesView.vue'), meta: { auth: true } },
  { path: '/units', name: 'units', component: () => import('../views/UnitsView.vue'), meta: { auth: true } },
  { path: '/profile', name: 'profile', component: () => import('../views/ProfileView.vue'), meta: { auth: true } },
  { path: '/:pathMatch(.*)*', name: 'notfound', component: () => import('../views/NotFoundView.vue') }
]

const router = createRouter({ history: createWebHashHistory(), routes })

// Route guard: pages marked meta.auth need a logged-in user
router.beforeEach((to) => {
  if (to.meta.auth && !auth.token) return { name: 'login' }
})

export default router

import { reactive } from 'vue'

const API = '/api/index.php'

// Shared login state, saved in localStorage so it survives page reloads
export const auth = reactive({
  user: JSON.parse(localStorage.getItem('user') || 'null'),
  token: localStorage.getItem('token') || ''
})

export function setSession(user, token) {
  auth.user = user
  auth.token = token
  if (user) {
    localStorage.setItem('user', JSON.stringify(user))
    localStorage.setItem('token', token)
  } else {
    localStorage.clear()
  }
}

export async function api(route, { method = 'GET', id = 0, body } = {}) {
  const res = await fetch(`${API}?route=${route}${id ? `&id=${id}` : ''}`, {
    method,
    headers: { 'Content-Type': 'application/json', 'X-Token': auth.token },
    body: body ? JSON.stringify(body) : undefined
  })
  const data = await res.json()
  if (!res.ok) throw new Error(data.error || 'Request failed')
  return data
}

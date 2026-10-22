import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
// base './' lets the built site work from any Mercury folder
export default defineConfig({ plugins: [vue()], base: './' })

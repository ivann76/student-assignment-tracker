
// for github pages deployment, uncomment the following code and comment out the code below it
// import { defineConfig } from 'vite'
// import vue from '@vitejs/plugin-vue'

// export default defineConfig({
//   plugins: [vue()],
//   base: '/student-assignment-tracker/'
  
// })

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
  plugins: [vue()],
  base: '/'
})
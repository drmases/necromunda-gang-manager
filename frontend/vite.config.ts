import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

export default defineConfig({
  plugins: [react()],
  base: '/necromunda-gang-manager/',
  server: {
    proxy: {
      '/necromunda-gang-manager/api': {
        target: 'https://globbin.se',
        changeOrigin: true,
      },
    },
  },
})

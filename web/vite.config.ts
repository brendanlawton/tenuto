import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import vueDevTools from 'vite-plugin-vue-devtools'
import tailwindcss from '@tailwindcss/vite'

const apiProxyTarget = process.env.VITE_PROXY_TARGET ?? 'http://localhost'

export default defineConfig({
  plugins: [
    tailwindcss(),
    vue(),
    vueDevTools(),
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    },
  },
  server: {
    proxy: {
      '/sanctum': { target: apiProxyTarget, changeOrigin: true },
      '/login': { target: apiProxyTarget, changeOrigin: true },
      '/logout': { target: apiProxyTarget, changeOrigin: true },
      '/register': { target: apiProxyTarget, changeOrigin: true },
      '/forgot-password': { target: apiProxyTarget, changeOrigin: true },
      '/reset-password': { target: apiProxyTarget, changeOrigin: true },
      '/email': { target: apiProxyTarget, changeOrigin: true },
      '/api': { target: apiProxyTarget, changeOrigin: true },
      '/auth': { target: apiProxyTarget, changeOrigin: true },
    },
  },
})

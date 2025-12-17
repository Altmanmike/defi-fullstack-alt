// frontend/vite.config.ts

import { defineConfig } from 'vitest/config'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
  plugins: [vue()],
  server: {
    host: true, // équivalent à --host
    port: 5173, // on force le port d'origine
    strictPort: true, // Si le port est pris, il s'arrête au lieu de changer
    watch: {
      usePolling: true, // Très important pour Windows/Docker pour voir les modifs de fichiers
    },
  },
  // ✅ NOUVELLE SECTION: Utilisation de 'resolve.alias' pour le mocking du CSS
  // Ceci est la manière la plus sûre de faire le mocking de fichiers d'assets.
  resolve: {
    alias: {
      "\\.(css|sass|scss)$": "vitest-asset.ts",
    },
  },

  test: {
    environment: "jsdom",
    globals: true,
    setupFiles: "./vitest.setup.ts",
    server: {
      deps: {
        inline: [/vuetify/],
      },
    },
  },
});
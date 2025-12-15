// frontend/vite.config.ts

import { defineConfig } from 'vitest/config'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
  plugins: [vue()],
  // ... (server et autres configurations)

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
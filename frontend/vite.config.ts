// frontend/vite.config.ts

import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue";

export default defineConfig({
  plugins: [vue()],
  // ✅ AJOUTER CETTE SECTION
  server: {
    host: "0.0.0.0", // Écoute sur toutes les interfaces réseau
    port: 5173, // Port défini dans docker-compose
    watch: {
      usePolling: true, // Souvent nécessaire pour les volumes montés sur Windows/WSL
    },
  },
});

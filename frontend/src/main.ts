// frontend/src/main.ts

import { createApp } from "vue";
import App from "./App.vue";

// Importations Vuetify
import "vuetify/styles";
import { createVuetify } from "vuetify";
import * as components from "vuetify/components";
import * as directives from "vuetify/directives";
import "@mdi/font/css/materialdesignicons.css"; // Nécessaire pour les icônes de Vuetify

const vuetify = createVuetify({
  components,
  directives,
  icons: {
    defaultSet: "mdi", // Utiliser les icônes Material Design
  },
});

createApp(App).use(vuetify).mount("#app");

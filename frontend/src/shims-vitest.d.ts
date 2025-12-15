// frontend/src/shims-vitest.d.ts

/// <reference types="vitest/globals" />

// Assure que l'objet global est connu pour le mocking de fetch
declare var global: any;

// Déclarez l'environnement de test pour que les types soient connus
declare module "vitest" {
  interface ProvidedContext {
    // Vous pouvez ajouter d'autres types personnalisés ici si besoin
  }
}

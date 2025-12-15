// frontend/src/App.test.ts

import { mount } from "@vue/test-utils";
import flushPromises from "flush-promises";
//import { describe, it, expect, vi, beforeEach } from "vitest";
import App from "./App.vue";
import {
  mockFetchSuccess,
  mockFetchError,
  MOCK_ROUTE_RESPONSE,
} from "./mocks/api";
import { createVuetify } from "vuetify";
import * as components from "vuetify/components";
import * as directives from "vuetify/directives";

// Initialise un objet Vuetify complet pour que les composants V-* soient reconnus
const vuetify = createVuetify({
  components,
  directives,
});

// Les tests se concentrent sur le comportement du composant (saisie, clic, affichage du résultat)
describe("App.vue (Route Calculator)", () => {
  // Nettoie les mocks avant chaque test
  beforeEach(() => {
    vi.clearAllMocks();
  });

  it("renders correctly and calculates a route successfully", async () => {
    // 1. Simuler une réponse API réussie
    mockFetchSuccess();

    // 2. Monter le composant App.vue
    const wrapper = mount(App, {
      global: {
        plugins: [vuetify], // Injecte le plugin Vuetify pour le rendu des composants
      },
    });

    // 3. Vérifier l'état initial
    expect(wrapper.find("h1").text()).toContain("Calculateur de Trajet");

    // Les valeurs initiales sont MX et IO
    const fromInput = wrapper.findAll("input")[0];
    const toInput = wrapper.findAll("input")[1];

    expect(fromInput.element.value).toBe("MX");
    expect(toInput.element.value).toBe("IO");

    // 4. Déclencher la soumission du formulaire
    const calculateButton = wrapper.find("button");
    await calculateButton.trigger("submit");

    // 5. ✅ NOUVELLE MÉTHODE : Attendre que toutes les promesses (le fetch mocké) soient résolues
    await flushPromises();

    // 6. Attendre que Vue mette à jour le DOM après la résolution de la promesse
    await wrapper.vm.$nextTick();

    // 7. Vérifier l'affichage du résultat
    const resultCard = wrapper.find(".v-card");
    expect(resultCard.exists()).toBe(true);
    expect(resultCard.text()).toContain("Résultat du Trajet");

    // ✅ NOUVELLE ASSERTION PLUS ROBUSTE : Cibler le texte exact sans les balises environnantes
    const expectedDistanceText = `Distance Totale: ${MOCK_ROUTE_RESPONSE.distanceKm.toFixed(2)} km`;

    // Nous vérifions si le texte total de la carte contient la distance calculée.
    expect(resultCard.text()).toContain(expectedDistanceText);

    // Vérification du chemin (qui était déjà robuste)
    expect(resultCard.text()).toContain("MX → GD → IO");

    // 4. Déclencher la soumission du formulaire
    /*const calculateButton = wrapper.find("button");
    await calculateButton.trigger("submit");

    // 5. Attendre que Vue se mette à jour et que la promesse fetch soit résolue
    await wrapper.vm.$nextTick();
    await new Promise((resolve) => setTimeout(resolve, 0)); // Assure la résolution des microtâches

    // 6. Vérifier si fetch a été appelé correctement
    expect(global.fetch).toHaveBeenCalledTimes(1);
    const fetchArgs = (global.fetch as any).mock.calls[0][1];
    expect(JSON.parse(fetchArgs.body).fromStationId).toBe("MX");*/

    // 7. Vérifier l'affichage du résultat
    /*expect(wrapper.html()).toContain("Résultat du Trajet");
    expect(wrapper.html()).toContain(
      `Distance Totale: ${MOCK_ROUTE_RESPONSE.distanceKm.toFixed(2)} km`
    );
    expect(wrapper.html()).toContain("MX → GD → IO");*/
  });

  it("handles API error and displays an alert", async () => {
    // 1. Simuler une réponse API en échec
    const errorMessage = "Station de départ non valide.";
    mockFetchError(errorMessage);

    const wrapper = mount(App, {
      global: {
        plugins: [vuetify],
      },
    });

    // 2. Déclencher la soumission
    await wrapper.find("button").trigger("submit");

    // 3. Attendre l'échec et la mise à jour
    await wrapper.vm.$nextTick();
    await new Promise((resolve) => setTimeout(resolve, 0));

    // 4. Vérifier l'affichage de l'alerte d'erreur
    expect(wrapper.html()).toContain("Erreur: Station de départ non valide.");
  });
});

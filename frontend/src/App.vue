<script setup lang="ts">
import { ref } from 'vue';
import { RouteRequest, RouteResponse } from './types';
import { VContainer, VForm, VTextField, VBtn, VAlert, VCard, VCardTitle, VCardText } from 'vuetify/components';

// L'URL d'accès au backend Docker. Utilisez 'http://localhost:8088' pour le dev local,
// ou le nom du service Docker si vous faites l'appel DANS le conteneur Frontend.
// Pour l'instant, restons sur localhost pour le développement local Vite :
const API_URL = 'http://localhost:8088/api/v1/route';

// État du formulaire
const fromStation = ref('MX');
const toStation = ref('IO');
const analyticCode = ref('FRONT_TEST_VUE');

// État de l'application
const result = ref<RouteResponse | null>(null);
const loading = ref(false);
const error = ref<string>('');

const handleSubmit = async () => {
    loading.value = true;
    error.value = '';
    result.value = null;

    const requestBody: RouteRequest = {
        fromStationId: fromStation.value.toUpperCase(),
        toStationId: toStation.value.toUpperCase(),
        analyticCode: analyticCode.value,
    };

    try {
        const response = await fetch(API_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(requestBody),
        });

        if (!response.ok) {
            const errorData = await response.json();
            const message = errorData.message || `Erreur de l'API: Statut ${response.status}`;
            throw new Error(message);
        }

        const data: RouteResponse = await response.json();
        result.value = data;

    } catch (err) {
        error.value = err instanceof Error ? err.message : 'Une erreur inconnue est survenue.';
    } finally {
        loading.value = false;
    }
};
</script>

<template>
  <VContainer>
    <h1>Calculateur de Trajet Ferroviaire (Vue/Vuetify)</h1>
    
    <VForm @submit.prevent="handleSubmit">
      <VTextField
        v-model="fromStation"
        label="Station de Départ (Ex: MX)"
        variant="outlined"
        required
        class="mt-4"
        @update:model-value="fromStation = fromStation.toUpperCase()"
      ></VTextField>

      <VTextField
        v-model="toStation"
        label="Station d'Arrivée (Ex: IO)"
        variant="outlined"
        required
        class="mt-4"
        @update:model-value="toStation = toStation.toUpperCase()"
      ></VTextField>

      <VBtn
        type="submit"
        color="primary"
        size="large"
        :loading="loading"
        class="mt-6"
      >
        Calculer Trajet
      </VBtn>
    </VForm>

    <VAlert v-if="error" type="error" class="mt-6">
      Erreur: {{ error }}
    </VAlert>

    <VCard v-if="result" class="mt-6">
      <VCardTitle>Résultat du Trajet (ID: {{ result.id.substring(0, 8) }}...)</VCardTitle>
      <VCardText>
        <p><strong>Distance Totale:</strong> {{ result.distanceKm.toFixed(2) }} km</p>
        <p class="mt-2"><strong>Chemin (stations):</strong></p>
        <p style="white-space: pre-wrap; font-family: monospace;">
          {{ result.path.join(' → ') }}
        </p>
      </VCardText>
    </VCard>

  </VContainer>
</template>
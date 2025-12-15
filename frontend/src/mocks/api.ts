// frontend/src/mocks/api.ts

import { RouteResponse } from "../types";

// La réponse que votre mock renverra
export const MOCK_ROUTE_RESPONSE: RouteResponse = {
  id: "mock-12345",
  fromStationId: "MX",
  toStationId: "IO",
  analyticCode: "TEST_VITEST",
  distanceKm: 115.35,
  path: ["MX", "GD", "IO"],
  createdAt: new Date().toISOString(),
};

// Simulation de l'objet global 'fetch'
export function mockFetchSuccess() {
  global.fetch = vi.fn(() =>
    Promise.resolve({
      ok: true,
      status: 201,
      json: () => Promise.resolve(MOCK_ROUTE_RESPONSE),
    } as Response)
  );
}

export function mockFetchError(message: string) {
  global.fetch = vi.fn(() =>
    Promise.resolve({
      ok: false,
      status: 400,
      json: () => Promise.resolve({ message: message }),
    } as Response)
  );
}
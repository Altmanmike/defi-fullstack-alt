// frontend/vitest.setup.ts

import { vi } from "vitest";

// @ts-ignore
global.fetch = vi.fn();

// ✅ CORRECTION DU MOCK RESIZEOBSERVER : plus simple et compatible TS
// Nous utilisons vi.fn() pour créer un constructeur mock
// et nous simulons les méthodes qu'il est censé contenir.

// @ts-ignore
global.ResizeObserver = vi.fn().mockImplementation(() => ({
  // Ces méthodes doivent exister pour satisfaire le prototype de ResizeObserver
  observe: vi.fn(),
  unobserve: vi.fn(),
  disconnect: vi.fn(),
}));

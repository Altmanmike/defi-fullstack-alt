// frontend/src/types.ts

export interface RouteRequest {
  fromStationId: string;
  toStationId: string;
  analyticCode: string;
}

export interface RouteResponse {
  id: string;
  fromStationId: string;
  toStationId: string;
  analyticCode: string;
  distanceKm: number;
  path: string[];
  createdAt: string;
}

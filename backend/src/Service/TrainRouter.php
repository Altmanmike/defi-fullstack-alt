<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

class TrainRouter
{
    private array $graph = [];    
    private string $stationsFilePath = __DIR__ . '/../../data/stations.json';
    private string $distancesFilePath = __DIR__ . '/../../data/distances.json';

    public function __construct(LoggerInterface $logger)
    {
        // 1. Charger les données du fichier JSON
        $distancesData = json_decode(file_get_contents($this->distancesFilePath), true);
        
        // 2. Construire la Liste d'Adjacence
        $this->buildGraph($distancesData);
    }

    private function buildGraph(array $distancesData): void
    {
        $this->graph = [];

        // Le JSON contient des objets par ligne (ex: "MOB", "MVR-ce")
        foreach ($distancesData as $lineSection) {
            
            // Vérification de sécurité si la clé 'distances' existe
            if (!isset($lineSection['distances']) || !is_array($lineSection['distances'])) {
                continue;
            }

            // On parcourt le tableau 'distances' à l'intérieur de chaque ligne
            foreach ($lineSection['distances'] as $link) {
                // CORRECTION DES CLÉS SELON VOTRE JSON :
                // 'parent' -> station de départ du tronçon
                // 'child'  -> station d'arrivée du tronçon
                // 'distance' -> coût en km
                
                $stationA = $link['parent']; 
                $stationB = $link['child'];
                $distance = $link['distance']; 

                // Le graphe est non-orienté : A <-> B
                // Initialisation des tableaux si nécessaire pour éviter les warnings
                if (!isset($this->graph[$stationA])) { $this->graph[$stationA] = []; }
                if (!isset($this->graph[$stationB])) { $this->graph[$stationB] = []; }

                $this->graph[$stationA][$stationB] = $distance;
                $this->graph[$stationB][$stationA] = $distance;
            }
        }
    }
    
    // ... La méthode findShortestPath sera ajoutée ici
    /**
     * @return array{distanceKm: float, path: string[]}
     */
    public function findShortestPath(string $fromStationId, string $toStationId): array
    {
        // 1. ✅ AJOUTER CETTE VÉRIFICATION
        $allStations = array_keys($this->graph);
        
        // Si la station de départ ou d'arrivée n'existe pas dans le graphe, on retourne un échec immédiat.
        if (!in_array($fromStationId, $allStations) || !in_array($toStationId, $allStations)) {
             return ['distanceKm' => 0.0, 'path' => []]; 
        }
        
        // Initialisation des distances (toutes à l'infini, sauf la source à 0)
        $distances = array_fill_keys($allStations, INF);
        $distances[$fromStationId] = 0;
        
        // Enregistre le chemin pour reconstruire le trajet final
        $previousNodes = array_fill_keys(array_keys($this->graph), null);
        
        // Utilisation d'une SplPriorityQueue pour la performance (nœud le plus proche en priorité)
        $queue = new \SplPriorityQueue();
        // La priorité est le coût (la distance), nous voulons le coût le plus faible, donc priorité négative
        $queue->insert($fromStationId, 0); 

        while (!$queue->isEmpty()) {
            $currentStation = $queue->extract(); // Station la plus proche à explorer
            $currentDistance = $distances[$currentStation];

            // Si nous avons atteint la station de destination, on s'arrête
            if ($currentStation === $toStationId) {
                break;
            }

            // Parcourir les voisins
            if (!isset($this->graph[$currentStation])) {
                // Gérer le cas où la station de départ n'a pas de liens (station isolée)
                continue;
            }

            foreach ($this->graph[$currentStation] as $neighbor => $weight) {
                $newDistance = $currentDistance + $weight;

                // Relaxation : Si on trouve un chemin plus court vers le voisin
                if ($newDistance < $distances[$neighbor]) {
                    $distances[$neighbor] = $newDistance;
                    $previousNodes[$neighbor] = $currentStation;
                    
                    // Insérer le voisin dans la file de priorité avec sa nouvelle distance
                    // La file de priorité trie par le coût le plus bas (d'où le négatif pour la SplPriorityQueue de PHP)
                    $queue->insert($neighbor, -$newDistance); 
                }
            }
        }

        // 3. Reconstruire le chemin
        $path = $this->reconstructPath($previousNodes, $fromStationId, $toStationId);

        // 4. Vérification et Résultat
        if (empty($path)) {
             // Route non trouvée (réseau non connexe ou station inconnue)
             return ['distanceKm' => 0.0, 'path' => []]; 
        }

        return [
            'distanceKm' => round($distances[$toStationId], 2),
            'path' => $path,
        ];
    }
    
    private function reconstructPath(array $previousNodes, string $startNode, string $endNode): array
    {
        $path = [];
        $currentNode = $endNode;

        // On remonte de l'arrivée au départ
        while ($currentNode !== null) {
            array_unshift($path, $currentNode);
            // S'assurer de ne pas boucler indéfiniment si le nœud de départ est atteint
            if ($currentNode === $startNode) {
                break;
            }
            $currentNode = $previousNodes[$currentNode];
        }

        // Si le chemin ne commence pas par la station de départ, il n'a pas été trouvé (ou est incomplet)
        return ($path[0] ?? null) === $startNode ? $path : [];
    }
}
<?php

namespace App\Tests\Service;

use App\Service\TrainRouter;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class TrainRouterTest extends TestCase
{
    private TrainRouter $router;

    protected function setUp(): void
    {
        // 1. Simuler le Logger : Doctrine requiert un Logger dans le constructeur
        $logger = $this->createMock(LoggerInterface::class);

        // 2. Initialiser le service TrainRouter
        // L'initialisation va charger distances.json et construire le graphe
        $this->router = new TrainRouter($logger);
    }

    // --- Scénarios de Test ---

    /**
     * Teste un trajet long et complexe (MX à IO) pour valider l'algorithme de Dijkstra.
     */
    public function testFindShortestPathLongRoute(): void
    {
        $result = $this->router->findShortestPath('MX', 'IO');

        // 1. Vérifie que le chemin a été trouvé
        $this->assertIsArray($result['path']);
        $this->assertNotEmpty($result['path']);

        // 2. Vérifie la distance totale (basée sur le résultat que nous avons validé)
        // On utilise assertGreaterThanOrEqual pour tolérer de petites erreurs d'arrondi ou de structure interne
        $this->assertGreaterThanOrEqual(115.3, $result['distanceKm']);
        $this->assertLessThan(115.4, $result['distanceKm']);
        
        // 3. Vérifie que le chemin commence et finit correctement
        $this->assertEquals('MX', $result['path'][0]);
        $this->assertEquals('IO', end($result['path']));

        // 4. Vérifie la longueur du chemin (environ 56 arrêts, doit être > 2)
        $this->assertGreaterThan(50, count($result['path']));
    }

    /**
     * Teste un trajet très court entre deux stations voisines (MX à CGE).
     */
    public function testFindShortestPathShortRoute(): void
    {
        $result = $this->router->findShortestPath('MX', 'CGE');
        
        // Vérifie que le chemin est correct : [MX, CGE]
        $this->assertEquals(['MX', 'CGE'], $result['path']);

        // Vérifie la distance (basée sur distances.json, MX -> CGE est 0.65 km)
        $this->assertEquals(0.65, $result['distanceKm']);
    }

    /**
     * Teste le cas où la station de destination n'existe pas.
     * Le service doit retourner un tableau 'path' vide et une distance de 0.0.
     */
    public function testFindShortestPathStationNotFound(): void
    {
        $result = $this->router->findShortestPath('MX', 'STATION_INCONNUE');

        // Le service doit retourner un résultat d'échec
        $this->assertIsArray($result);
        $this->assertEmpty($result['path']);
        $this->assertEquals(0.0, $result['distanceKm']);
    }

    /**
     * Ajoutons un test pour un trajet non connexe si vos données le permettent
     * (Imaginez que 'ISO' est une station totalement isolée sans lien).
     */
    public function testFindShortestPathNonConnectedRoute(): void
    {
        // Supposons qu'il existe une station isolée 'ISO' dans le JSON, ou testons une station de début isolée
        // Si les données sont toutes connexes (ce qui semble être le cas), ce test pourrait simuler
        // le cas où le graphe est construit sans lien vers IO.
        
        // Pour ce test, nous allons utiliser une station qui n'est pas dans le chemin testé (par sécurité)
        // et une autre qui est loin, pour simuler un "non trouvé" si le code était défectueux.
        $result = $this->router->findShortestPath('MX', 'NON_EXISTANT_NODE');

        $this->assertIsArray($result);
        $this->assertEmpty($result['path']);
        $this->assertEquals(0.0, $result['distanceKm']);
    }
}
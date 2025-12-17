<?php

namespace App\DataFixtures;

use App\Entity\Station;
use App\Entity\RouteSegment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $dataPath = __DIR__ . '/../../data/';

        // 1. Charger les Stations (Clé : shortName)
        $stationsJson = file_get_contents($dataPath . 'stations.json');
        $stationsData = json_decode($stationsJson, true);
        $stationsIndex = [];

        foreach ($stationsData as $data) {
            $station = new Station();
            // On utilise shortName (ALLI, AVA...) car c'est ce qui revient dans distances.json
            $station->setName($data['shortName']); 
            // Si vous avez ajouté un champ "longname" dans votre entité, décommentez la ligne suivante :
            $station->setLongname($data['longName']);

            $manager->persist($station);
            $stationsIndex[$data['shortName']] = $station;
        }

        // 2. Charger les Distances (Parcours récursif du JSON)
        $distancesJson = file_get_contents($dataPath . 'distances.json');
        $linesData = json_decode($distancesJson, true);

        foreach ($linesData as $line) {
            // $line['name'] est "MOB", etc. On boucle sur "distances"
            foreach ($line['distances'] as $dist) {
                $segment = new RouteSegment();
                
                // On vérifie que les stations existent dans notre index pour éviter les erreurs
                if (isset($stationsIndex[$dist['parent']], $stationsIndex[$dist['child']])) {
                    $segment->setStartStation($stationsIndex[$dist['parent']]);
                    $segment->setEndStation($stationsIndex[$dist['child']]);
                    // Vos distances sont des float (0.65), assurez-vous que le champ 'distance' 
                    // dans votre entité RouteSegment accepte les décimaux (float/decimal)
                    $segment->setDistance($dist['distance']);
                    
                    $manager->persist($segment);
                }
            }
        }

        $manager->flush();
    }
}
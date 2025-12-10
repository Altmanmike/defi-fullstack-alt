<?php
// backend/src/Controller/RouteController.php

namespace App\Controller;

use App\Service\TrainRouter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RouteController extends AbstractController
{
    public function __construct(
        private TrainRouter $trainRouter,
        // Si vous utilisez Doctrine pour la persistance (étape bonus)
        // private EntityManagerInterface $entityManager, 
    ) {}

    #[Route('/api/v1/routes', methods: ['POST'])]
    public function calculateRoute(Request $request): Response
    {
        // 1. Désérialisation et Validation de la Requête (RouteRequest)
        $data = json_decode($request->getContent(), true);
                
        if (!isset($data['fromStationId']) || !isset($data['toStationId']) || !isset($data['analyticCode'])) {
            return $this->json([
                'code' => 'BAD_REQUEST', 
                'message' => 'Champs fromStationId, toStationId et analyticCode sont requis.'
            ], Response::HTTP_BAD_REQUEST);
        }
        
        // Validation basique des champs requis (comme spécifié par OpenAPI)
        if (empty($data['fromStationId']) || empty($data['toStationId']) || empty($data['analyticCode'])) {
            return $this->json([
                'code' => 'BAD_REQUEST', 
                'message' => 'Champs fromStationId, toStationId et analyticCode sont requis.'
            ], Response::HTTP_BAD_REQUEST);
        }

        $from = $data['fromStationId'];
        $to = $data['toStationId'];
        $analyticCode = $data['analyticCode'];

        // 2. Calcul du Trajet
        try {
            $routeResult = $this->trainRouter->findShortestPath($from, $to);
        } catch (\RuntimeException $e) {
            // Erreur si les fichiers de données sont manquants
            return $this->json(['code' => 'SERVER_ERROR', 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // 3. Gestion des Erreurs de Routage (Code 422 - Données non valides)
        if (empty($routeResult['path'])) {
            return $this->json([
                'code' => 'ROUTE_NOT_FOUND', 
                'message' => 'Aucun trajet trouvé entre les stations ou stations inconnues.'
            ], Response::HTTP_UNPROCESSABLE_ENTITY); // 422
        }

        // 4. Persistance (Étape Bonus / Requise pour les Analytics)
        // Dans un vrai projet, vous feriez ici une insertion en base de données
        // $routeEntity = new Route($from, $to, $analyticCode, $routeResult['distanceKm'], $routeResult['path']);
        // $this->entityManager->persist($routeEntity);
        // $this->entityManager->flush();

        // 5. Réponse 201 (Trajet calculé et enregistré)
        $response = [
            'id' => uniqid(), // ID simulé, utilisez l'ID de la DB en vrai
            'fromStationId' => $from,
            'toStationId' => $to,
            'analyticCode' => $analyticCode,
            'distanceKm' => $routeResult['distanceKm'],
            'path' => $routeResult['path'],
            'createdAt' => (new \DateTime())->format(\DateTimeInterface::ATOM),
        ];

        return $this->json($response, Response::HTTP_CREATED); // 201
    }
}
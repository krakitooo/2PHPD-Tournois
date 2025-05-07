<?php

namespace App\Controller;

use App\Entity\Tournament;
use App\Entity\User;
use App\Repository\TournamentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/tournaments')]
class TournamentController extends AbstractController
{
    #[Route('', name: 'tournament_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(TournamentRepository $repository): JsonResponse
    {
        $tournaments = $repository->findAll();
        $today = new \DateTime();
        $data = [];
    
        foreach ($tournaments as $tournament) {
            if ($today < $tournament->getStartDate()) {
                $status = 'à venir';
            } elseif ($today >= $tournament->getStartDate() && $today <= $tournament->getEndDate()) {
                $status = 'en cours';
            } else {
                $status = 'terminé';
            }
    
            $data[] = [
                'id' => $tournament->getId(),
                'tournamentName' => $tournament->getTournamentName(),
                'location' => $tournament->getLocation(),
                'startDate' => $tournament->getStartDate()->format('Y-m-d'),
                'endDate' => $tournament->getEndDate()->format('Y-m-d'),
                'description' => $tournament->getDescription(),
                'maxParticipants' => $tournament->getMaxParticipants(),
                'sport' => $tournament->getSport(),
                'status' => $status
            ];
        }
    
        return $this->json($data);
    }
    
    #[Route('/{id}', name: 'tournament_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(Tournament $tournament): JsonResponse
    {
        $today = new \DateTime();
    
        if ($today < $tournament->getStartDate()) {
            $status = 'à venir';
        } elseif ($today >= $tournament->getStartDate() && $today <= $tournament->getEndDate()) {
            $status = 'en cours';
        } else {
            $status = 'terminé';
        }
    
        // Crée manuellement le tableau de données à retourner
        $data = [
            'id' => $tournament->getId(),
            'tournamentName' => $tournament->getTournamentName(),
            'location' => $tournament->getLocation(),
            'startDate' => $tournament->getStartDate()->format('Y-m-d'),
            'endDate' => $tournament->getEndDate()->format('Y-m-d'),
            'description' => $tournament->getDescription(),
            'maxParticipants' => $tournament->getMaxParticipants(),
            'sport' => $tournament->getSport(),
            'status' => $status
        ];
    
        return $this->json($data);
    }
    

    #[Route('', name: 'tournament_create', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function create(Request $request, EntityManagerInterface $em, SerializerInterface $serializer): JsonResponse
    {
        $currentUser = $this->getUser();  // récupère l'utilisateur connecté
    
        $data = json_decode($request->getContent(), true);
    
        $tournament = new Tournament();
        $tournament->setTournamentName($data['tournamentName'] ?? '');
        $tournament->setStartDate(new \DateTime($data['startDate']));
        $tournament->setEndDate(new \DateTime($data['endDate']));
        $tournament->setLocation($data['location'] ?? null);
        $tournament->setDescription($data['description'] ?? '');
        $tournament->setMaxParticipants($data['maxParticipants'] ?? 0);
        $tournament->setSport($data['sport'] ?? '');
        $tournament->setOrganizer($currentUser);
    
        $em->persist($tournament);
        $em->flush();
    
        $json = $serializer->serialize($tournament, 'json', ['groups' => 'tournament:read']);
        return new JsonResponse($json, Response::HTTP_CREATED, [], true);
    }
    
    #[Route('/{id}', name: 'tournament_update', methods: ['PUT'])]
    #[IsGranted('ROLE_USER')]
    public function update(Request $request, Tournament $tournament, EntityManagerInterface $em, SerializerInterface $serializer): JsonResponse
    {
        $currentUser = $this->getUser();
        if ($tournament->getOrganizer() !== $currentUser && !in_array('ROLE_ADMIN', $currentUser->getRoles())) {
            return $this->json(['message' => 'Access denied.'], Response::HTTP_FORBIDDEN);
        }

        $serializer->deserialize($request->getContent(), Tournament::class, 'json', [
            'object_to_populate' => $tournament,
        ]);

        $em->flush();
        $json = $serializer->serialize($tournament, 'json', ['groups' => 'tournament:read']);
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'tournament_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Tournament $tournament, EntityManagerInterface $em): JsonResponse
    {
        $currentUser = $this->getUser();
        if ($tournament->getOrganizer() !== $currentUser && !in_array('ROLE_ADMIN', $currentUser->getRoles())) {
            return $this->json(['message' => 'Access denied.'], Response::HTTP_FORBIDDEN);
        }
        
        $em->remove($tournament);
        $em->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}

<?php

namespace App\Controller;

use App\Entity\Registration;
use App\Entity\Tournament;
use App\Entity\User;
use App\Repository\RegistrationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/tournaments')]
class RegistrationController extends AbstractController
{
    #[Route('/{id}/registrations', name: 'registration_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(Tournament $tournament, RegistrationRepository $repo, SerializerInterface $serializer): JsonResponse
    {
        $currentUser = $this->getUser();
        if ($tournament->getOrganizer() !== $currentUser && !in_array('ROLE_ADMIN', $currentUser->getRoles())) {
            return $this->json(['message' => 'Access denied.'], Response::HTTP_FORBIDDEN);
        }
        
        $registrations = $repo->findBy(['tournament' => $tournament]);
        $json = $serializer->serialize($registrations, 'json', ['groups' => 'registration:read']);
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}/registrations', name: 'registration_create', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function create(Tournament $tournament, Request $request, EntityManagerInterface $em, SerializerInterface $serializer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $userId = $data['player_id'] ?? null;

        if (!$userId) {
            return new JsonResponse(['error' => 'player_id is required'], Response::HTTP_BAD_REQUEST);
        }

        $player = $em->getRepository(User::class)->find($userId);
        if (!$player) {
            return new JsonResponse(['error' => 'Player not found'], Response::HTTP_NOT_FOUND);
        }

        $registration = new Registration();
        $registration->setPlayer($player);
        $registration->setTournament($tournament);
        $registration->setRegistrationDate(new \DateTime());
        $registration->setStatus('en attente');

        $em->persist($registration);
        $em->flush();

        $json = $serializer->serialize($registration, 'json', ['groups' => 'registration:read']);
        return new JsonResponse($json, Response::HTTP_CREATED, [], true);
    }

    #[Route('/{idTournament}/registrations/{idRegistration}', name: 'registration_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Registration $idRegistration, EntityManagerInterface $em): JsonResponse
    {
        $currentUser = $this->getUser();
        $tournament = $idRegistration->getTournament();
        if ($tournament->getOrganizer() !== $currentUser && !in_array('ROLE_ADMIN', $currentUser->getRoles())) {
            return $this->json(['message' => 'Access denied.'], Response::HTTP_FORBIDDEN);
        }

        $em->remove($idRegistration);
        $em->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}

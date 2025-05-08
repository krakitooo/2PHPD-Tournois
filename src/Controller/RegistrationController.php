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
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/api/tournaments')]
class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
    
        if (!$data || !isset($data['username'], $data['password'])) {
            return new JsonResponse(['error' => 'Données incomplètes'], 400);
        }
    
        $user = new User();
        $user->setUsername($data['username']);
        $user->setFirstName($data['firstName'] ?? '');
        $user->setLastName($data['lastName'] ?? '');
        $user->setEmailAddress($data['emailAddress'] ?? '');
        $user->setPassword($passwordHasher->hashPassword($user, $data['password']));
        $user->setStatus('actif');
    
        $em->persist($user);
        $em->flush();
    
        return new JsonResponse(['message' => 'Utilisateur créé avec succès']);
    }
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
    public function create(Tournament $id, Request $request, EntityManagerInterface $em, SerializerInterface $serializer): JsonResponse
    {
        $currentUser = $this->getUser();
    
        // Vérifier si déjà inscrit
        $existingRegistration = $em->getRepository(Registration::class)->findOneBy([
            'player' => $currentUser,
            'tournament' => $id
        ]);
    
        if ($existingRegistration) {
            return $this->json(['message' => 'You are already registered to this tournament.'], Response::HTTP_BAD_REQUEST);
        }
    
        $registration = new Registration();
        $registration->setPlayer($currentUser);
        $registration->setTournament($id);
        $registration->setRegistrationDate(new \DateTime());
        $registration->setStatus('en attente');
    
        $em->persist($registration);
        $em->flush();
    
        $json = $serializer->serialize($registration, 'json', ['groups' => 'registration:read']);
        return new JsonResponse($json, Response::HTTP_CREATED, [], true);
    }
    

    #[Route('/{idTournament}/registrations/{idRegistration}', name: 'registration_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Tournament $idTournament, Registration $idRegistration, EntityManagerInterface $em): JsonResponse
    {
        $currentUser = $this->getUser();
    
        // Vérifier que l'inscription correspond bien au tournoi donné
        if ($idRegistration->getTournament()->getId() !== $idTournament->getId()) {
            return $this->json(['message' => 'Registration does not belong to this tournament.'], Response::HTTP_BAD_REQUEST);
        }
    
        // Autorisé si orga du tournoi, admin ou joueur inscrit
        if ($idRegistration->getPlayer() !== $currentUser &&
            $idTournament->getOrganizer() !== $currentUser &&
            !in_array('ROLE_ADMIN', $currentUser->getRoles())) {
            return $this->json(['message' => 'Access denied.'], Response::HTTP_FORBIDDEN);
        }
    
        $em->remove($idRegistration);
        $em->flush();
    
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
    
    #[Route('/{idTournament}/registrations/{idRegistration}/confirm', name: 'registration_confirm', methods: ['PUT'])]
    #[IsGranted('ROLE_USER')]
    public function confirm(Tournament $idTournament, Registration $idRegistration, EntityManagerInterface $em): JsonResponse
    {
        $currentUser = $this->getUser();

        // Vérifier que l'inscription correspond bien au tournoi
        if ($idRegistration->getTournament()->getId() !== $idTournament->getId()) {
            return $this->json(['message' => 'Registration does not belong to this tournament.'], Response::HTTP_BAD_REQUEST);
        }

        // Vérifier que seul l'organisateur ou un admin peut confirmer
        if ($idTournament->getOrganizer() !== $currentUser && !in_array('ROLE_ADMIN', $currentUser->getRoles())) {
            return $this->json(['message' => 'Access denied.'], Response::HTTP_FORBIDDEN);
        }

        // Mettre à jour le statut
        $idRegistration->setStatus('confirmée');
        $em->flush();

        return $this->json(['message' => 'Registration confirmed.']);
    }
}

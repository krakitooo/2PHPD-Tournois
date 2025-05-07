<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class PlayerController extends AbstractController
{
    #[Route('/register', name: 'player_register', methods: ['POST'])]
    public function register(Request $request, EntityManagerInterface $em, SerializerInterface $serializer, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');
        $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
        $user->setPassword($hashedPassword);
        $user->setStatus('actif');
        $user->setRoles(['ROLE_USER']);
        $em->persist($user);
        $em->flush();

        $json = $serializer->serialize($user, 'json', ['groups' => 'user:read']);
        return new JsonResponse($json, Response::HTTP_CREATED, [], true);
    }

    #[Route('/api/players', name: 'player_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function index(UserRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $users = $repository->findAll();
        $json = $serializer->serialize($users, 'json', ['groups' => 'user:read']);
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route('/api/players/{id}', name: 'player_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(User $user, SerializerInterface $serializer): JsonResponse
    {
        $currentUser = $this->getUser();
        if ($currentUser !== $user && !in_array('ROLE_ADMIN', $currentUser->getRoles())) {
            return $this->json(['message' => 'Access denied.'], Response::HTTP_FORBIDDEN);
        }

        $json = $serializer->serialize($user, 'json', ['groups' => 'user:read']);
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route('/api/players/{id}', name: 'player_update', methods: ['PUT'])]
    #[IsGranted('ROLE_USER')]
    public function update(Request $request, User $user, EntityManagerInterface $em, SerializerInterface $serializer, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $currentUser = $this->getUser();
        if ($currentUser !== $user && !in_array('ROLE_ADMIN', $currentUser->getRoles())) {
            return $this->json(['message' => 'Access denied.'], Response::HTTP_FORBIDDEN);
        }

        $serializer->deserialize($request->getContent(), User::class, 'json', ['object_to_populate' => $user]);
        
        $newPassword = $user->getPassword();
        if ($newPassword) {
            $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
            $user->setPassword($hashedPassword);
        }

        $em->flush();
        $json = $serializer->serialize($user, 'json', ['groups' => 'user:read']);
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route('/api/players/{id}', name: 'player_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(User $user, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($user);
        $em->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/players/{id}/promote', name: 'player_promote', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function promoteToAdmin(int $id, UserRepository $userRepository, EntityManagerInterface $em): JsonResponse
    {
        $user = $userRepository->find($id);

        if (!$user) {
            return $this->json(['message' => 'User not found.'], Response::HTTP_NOT_FOUND);
        }

        $roles = $user->getRoles();

        if (!in_array('ROLE_ADMIN', $roles)) {
            $roles[] = 'ROLE_ADMIN';
            $user->setRoles($roles);
            $em->flush();
        }

        return $this->json(['message' => 'User promoted to admin.']);
    }
}

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

class PlayerController extends AbstractController
{
    #[Route('/register', name: 'player_register', methods: ['POST'])]
    public function register(Request $request, EntityManagerInterface $em, SerializerInterface $serializer, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');
        $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
        $user->setPassword($hashedPassword);
        $em->persist($user);
        $em->flush();

        $json = $serializer->serialize($user, 'json', ['groups' => 'user:read']);
        return new JsonResponse($json, Response::HTTP_CREATED, [], true);
    }

    #[Route('/api/players', name: 'player_index', methods: ['GET'])]
    public function index(UserRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $users = $repository->findAll();
        $json = $serializer->serialize($users, 'json', ['groups' => 'user:read']);
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route('/api/players/{id}', name: 'player_show', methods: ['GET'])]
    public function show(User $user, SerializerInterface $serializer): JsonResponse
    {
        $json = $serializer->serialize($user, 'json', ['groups' => 'user:read']);
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route('/api/players/{id}', name: 'player_update', methods: ['PUT'])]
    public function update(Request $request, User $user, EntityManagerInterface $em, SerializerInterface $serializer, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
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
    public function delete(User $user, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($user);
        $em->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}

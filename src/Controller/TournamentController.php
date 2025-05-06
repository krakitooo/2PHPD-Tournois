<?php

namespace App\Controller;

use App\Entity\Tournament;
use App\Repository\TournamentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/tournaments')]
class TournamentController extends AbstractController
{
    #[Route('', name: 'tournament_index', methods: ['GET'])]
    public function index(TournamentRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $tournaments = $repository->findAll();
        $json = $serializer->serialize($tournaments, 'json', ['groups' => 'tournament:read']);
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'tournament_show', methods: ['GET'])]
    public function show(Tournament $tournament, SerializerInterface $serializer): JsonResponse
    {
        $json = $serializer->serialize($tournament, 'json', ['groups' => 'tournament:read']);
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route('', name: 'tournament_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, SerializerInterface $serializer): JsonResponse
    {
        $tournament = $serializer->deserialize($request->getContent(), Tournament::class, 'json');
        $em->persist($tournament);
        $em->flush();
        $json = $serializer->serialize($tournament, 'json', ['groups' => 'tournament:read']);
        return new JsonResponse($json, Response::HTTP_CREATED, [], true);
    }

    #[Route('/{id}', name: 'tournament_update', methods: ['PUT'])]
    public function update(Request $request, Tournament $tournament, EntityManagerInterface $em, SerializerInterface $serializer): JsonResponse
    {
        $data = $serializer->deserialize($request->getContent(), Tournament::class, 'json', ['object_to_populate' => $tournament]);
        $em->flush();
        $json = $serializer->serialize($data, 'json', ['groups' => 'tournament:read']);
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'tournament_delete', methods: ['DELETE'])]
    public function delete(Tournament $tournament, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($tournament);
        $em->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}

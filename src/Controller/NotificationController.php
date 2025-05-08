<?php

namespace App\Controller;

use App\Repository\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/notifications')]
class NotificationController extends AbstractController
{
    #[Route('', name: 'notification_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(NotificationRepository $notificationRepository, SerializerInterface $serializer): JsonResponse
    {
        $user = $this->getUser();
        $notifications = $notificationRepository->findBy(['user' => $user]);

        $json = $serializer->serialize($notifications, 'json', ['groups' => 'notification:read']);
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }
}

<?php
namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class MeApiController extends AbstractController
{
    #[Route('/api/me', name: 'api_me', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        return $this->json([
            'id'       => $user->getId(),
            'username' => $user->getUsername(),
            'firstName'=> $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'email'    => $user->getEmailAddress(),
            'roles'    => $user->getRoles(),
        ]);
    }
}

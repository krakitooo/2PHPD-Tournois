<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Routing\Annotation\Route;

class LogoutController
{
    #[Route('/logout', name: 'app_logout', methods: ['POST'])]
    public function logout(): JsonResponse
    {
        // Supprime le cookie BEARER en le réécrivant avec une date expirée
        $response = new JsonResponse(['message' => 'Déconnexion réussie']);

        $response->headers->setCookie(
            Cookie::create(
                'BEARER',
                '',                         // valeur vide
                new \DateTime('-1 hour'),   // date dans le passé
                '/',
                null,
                true,    // secure
                true,    // httpOnly
                false,
                'strict'
            )
        );

        return $response;
    }
}

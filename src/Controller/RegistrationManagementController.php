<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RegistrationManagementController extends AbstractController
{
    #[Route('/registration/management', name: 'app_registration_management')]
    public function index(): Response
    {
        return $this->render('registration_management/index.html.twig', [
            'controller_name' => 'RegistrationManagementController',
        ]);
    }

    //GET récupère la liste des inscriptions pour un tournoi spécifique


    //POST inscris un joueur à un tournoi spécifique

    //DELETE Annule l'inscription d'un joueur à un tournoi spécifique

    //
}

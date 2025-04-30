<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TournamentManagementController extends AbstractController
{
    #[Route('/tournament/management', name: 'app_tournament_management')]
    public function index(): Response
    {
        return $this->render('tournament_management/index.html.twig', [
            'controller_name' => 'TournamentManagementController',
        ]);
    }

    //récupère la liste des tournois

    //crée un nouveau tournoi

    //récupère les détails d'un tournoi spécifique

    //mets à jour les informations d'un tournoi spécifique

    //supprime un tournoi spécifique
}

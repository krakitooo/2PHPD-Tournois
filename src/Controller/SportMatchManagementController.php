<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SportMatchManagementController extends AbstractController
{
    #[Route('/sport/match/management', name: 'app_sport_match_management')]
    public function index(): Response
    {
        return $this->render('sport_match_management/index.html.twig', [
            'controller_name' => 'SportMatchManagementController',
        ]);
    }

    //GET récupère la liste des parties pour un tournoi spécifique

    //POST crée une nouvelle partie pour un tournoi spécifique

    //GET récupère les détails d'une partie spécifique

    //PUT mets à jour les résultats d'une partie spécifique

    //DELETE supprime une partie spécifique

}

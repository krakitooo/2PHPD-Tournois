<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PlayerManagementController extends AbstractController
{
    #[Route('/player/management', name: 'app_player_management')]
    public function index(): Response
    {
        return $this->render('player_management/index.html.twig', [
            'controller_name' => 'PlayerManagementController',
        ]);
    }
    //GET récupère la liste des joueurs

    //POST créer un utilisateur

    //GET récupère les détails d'un joueur spécifique

    //PUT mets à jour les informations d'un joueur spécifique
    
    //DELETE supprime un joueur spécifique


}

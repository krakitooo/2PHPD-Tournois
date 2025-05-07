<?php

namespace App\Controller;

use App\Repository\TournamentRepository;
use App\Repository\UserRepository;
use App\Repository\RegistrationRepository;
use App\Repository\SportMatchRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Core\Security;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin_dashboard', methods: ['GET'])]
    public function dashboard(
        TournamentRepository $tournamentRepo,
        UserRepository $userRepo,
        RegistrationRepository $registrationRepo,
        SportMatchRepository $sportMatchRepo
    ): Response {
        $user = $this->security->getUser();
    
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }
    
        return $this->render('admin/dashboard.html.twig', [
            'tournaments' => $tournamentRepo->findAll(),
            'users' => $userRepo->findAll(),
            'registrations' => $registrationRepo->findAll(),
            'matches' => $sportMatchRepo->findAll(),
            'user' => $user, 
        ]);
    }
}

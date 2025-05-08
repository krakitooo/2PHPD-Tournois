<?php

namespace App\Controller\Admin;

use App\Repository\UserRepository;
use App\Repository\TournamentRepository;
use App\Repository\RegistrationRepository;
use App\Repository\SportMatchRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminDashboardController extends AbstractController
{
    #[Route('/', name: 'admin_dashboard')]
    public function index(
        UserRepository $userRepo,
        TournamentRepository $tournamentRepo,
        RegistrationRepository $registrationRepo,
        SportMatchRepository $sportMatchRepo
    ): Response {
        $totalUsers = $userRepo->count([]);
        $totalTournaments = $tournamentRepo->count([]);
        $totalRegistrations = $registrationRepo->count([]);
        $totalMatches = $sportMatchRepo->count([]);

        return $this->render('admin/dashboard.html.twig', [
            'totalUsers' => $totalUsers,
            'totalTournaments' => $totalTournaments,
            'totalRegistrations' => $totalRegistrations,
            'totalMatches' => $totalMatches,
        ]);
    }
}

<?php

namespace App\Controller\Admin;

use App\Entity\SportMatch;
use App\Entity\Tournament;
use App\Entity\Registration;
use App\Form\AdminSportMatchType;
use App\Repository\SportMatchRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/matches')]
class AdminSportMatchController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'admin_sportmatches_index')]
    public function index(SportMatchRepository $repo): Response
    {
        return $this->render('admin/sportmatch/index.html.twig', [
            'matches' => $repo->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin_sportmatches_new')]
    public function new(Request $request): Response
    {
        $match = new SportMatch();
        $form = $this->createForm(AdminSportMatchType::class, $match);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($match);
            $this->entityManager->flush();

            $this->addFlash('success', 'Match créé avec succès.');
            return $this->redirectToRoute('admin_sportmatches_index');
        }

        return $this->render('admin/sportmatch/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_sportmatches_edit')]
    public function edit(Request $request, SportMatch $match): Response
    {
        $form = $this->createForm(AdminSportMatchType::class, $match);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            
            $this->addFlash('success', 'Match modifié avec succès.');
            return $this->redirectToRoute('admin_sportmatches_index');
        }

        return $this->render('admin/sportmatch/edit.html.twig', [
            'form' => $form->createView(),
            'match' => $match,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_sportmatches_delete')]
    public function delete(SportMatch $match): Response
    {
        $this->entityManager->remove($match);
        $this->entityManager->flush();

        $this->addFlash('success', 'Match supprimé.');
        return $this->redirectToRoute('admin_sportmatches_index');
    }

    #[Route("/tournaments/{id}/players", name: "admin_tournament_confirmed_players", methods: ["GET"])]
    public function getConfirmedPlayers(Tournament $tournament): JsonResponse
    {
        $confirmedPlayers = $this->entityManager->getRepository(Registration::class)
            ->createQueryBuilder('r')
            ->join('r.player', 'u')
            ->where('r.tournament = :tournament')
            ->andWhere('r.status = :status')
            ->setParameter('tournament', $tournament)
            ->setParameter('status', 'confirmée')
            ->getQuery()
            ->getResult();
        
        $players = array_map(function($registration) {
            $player = $registration->getPlayer();
            return [
                'id' => $player->getId(),
                'username' => $player->getUsername(),
            ];
        }, $confirmedPlayers);
        
        return new JsonResponse($players);
    }
}
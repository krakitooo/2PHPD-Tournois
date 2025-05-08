<?php

namespace App\Controller\Admin;

use App\Entity\Tournament;
use App\Form\AdminTournamentType;
use App\Repository\TournamentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/tournaments')]
#[IsGranted('ROLE_ADMIN')]
class AdminTournamentController extends AbstractController
{
    #[Route('/', name: 'admin_tournaments_index')]
    public function index(TournamentRepository $tournamentRepository): Response
    {
        return $this->render('admin/tournaments/index.html.twig', [
            'tournaments' => $tournamentRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin_tournaments_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $tournament = new Tournament();
        $form = $this->createForm(AdminTournamentType::class, $tournament);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($tournament);
            $em->flush();

            $this->addFlash('success', 'Tournoi ajouté avec succès.');
            return $this->redirectToRoute('admin_tournaments_index');
        }

        return $this->render('admin/tournaments/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_tournaments_edit')]
    public function edit(Request $request, Tournament $tournament, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(AdminTournamentType::class, $tournament);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Tournoi modifié avec succès.');
            return $this->redirectToRoute('admin_tournaments_index');
        }

        return $this->render('admin/tournaments/edit.html.twig', [
            'form' => $form->createView(),
            'tournament' => $tournament,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_tournaments_delete')]
    public function delete(Tournament $tournament, EntityManagerInterface $em): Response
    {
        $em->remove($tournament);
        $em->flush();

        $this->addFlash('success', 'Tournoi supprimé avec succès.');
        return $this->redirectToRoute('admin_tournaments_index');
    }
}

<?php

namespace App\Controller\Admin;

use App\Entity\SportMatch;
use App\Form\AdminSportMatchType;
use App\Repository\SportMatchRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/matches')]
class AdminSportMatchController extends AbstractController
{
    #[Route('/', name: 'admin_sportmatches_index')]
    public function index(SportMatchRepository $repo): Response
    {
        return $this->render('admin/sportmatch/index.html.twig', [
            'matches' => $repo->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin_sportmatches_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $match = new SportMatch();
        $form = $this->createForm(AdminSportMatchType::class, $match);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($match);
            $em->flush();

            $this->addFlash('success', 'Match ajouté.');
            return $this->redirectToRoute('admin_sportmatches_index');
        }

        return $this->render('admin/sportmatch/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_sportmatches_edit')]
    public function edit(Request $request, SportMatch $match, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(AdminSportMatchType::class, $match);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Match modifié.');
            return $this->redirectToRoute('admin_sportmatches_index');
        }

        return $this->render('admin/sportmatch/edit.html.twig', [
            'form' => $form->createView(),
            'match' => $match,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_sportmatches_delete')]
    public function delete(SportMatch $match, EntityManagerInterface $em): Response
    {
        $em->remove($match);
        $em->flush();

        $this->addFlash('success', 'Match supprimé.');
        return $this->redirectToRoute('admin_sportmatches_index');
    }
}

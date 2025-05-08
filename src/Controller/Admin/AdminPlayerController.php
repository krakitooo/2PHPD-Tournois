<?php

namespace App\Controller\Admin;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\AdminPlayerType;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/admin/players')]
#[IsGranted('ROLE_ADMIN')]
class AdminPlayerController extends AbstractController
{
    #[Route('/', name: 'admin_players_index')]
    public function index(UserRepository $userRepository): Response
    {
        $players = $userRepository->findAll();

        return $this->render('admin/players/index.html.twig', [
            'players' => $players,
        ]);
    }
    
    #[Route('/{id}/edit', name: 'admin_players_edit')]
    public function edit(int $id, UserRepository $userRepository, Request $request, EntityManagerInterface $em): Response
    {
        $player = $userRepository->find($id);
    
        if (!$player) {
            throw $this->createNotFoundException('Joueur introuvable.');
        }
    
        $form = $this->createForm(AdminPlayerType::class, $player, [
            'require_password' => false,
        ]);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
    
            $this->addFlash('success', 'Joueur mis à jour avec succès.');
    
            return $this->redirectToRoute('admin_players_index');
        }
    
        return $this->render('admin/players/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    

    #[Route('/new', name: 'admin_players_new')]
    public function new(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $player = new \App\Entity\User();

        $player->setStatus('actif'); 
    
        $form = $this->createForm(AdminPlayerType::class, $player);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            $hashedPassword = $passwordHasher->hashPassword($player, $plainPassword);
            $player->setPassword($hashedPassword);
    
            $em->persist($player);
            $em->flush();
    
            $this->addFlash('success', 'Joueur ajouté avec succès.');
    
            return $this->redirectToRoute('admin_players_index');
        }
    
        return $this->render('admin/players/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_players_delete')]
    public function delete(int $id, UserRepository $userRepository, EntityManagerInterface $em): Response
    {
        $player = $userRepository->find($id);

        if (!$player) {
            throw $this->createNotFoundException('Joueur introuvable.');
        }

        $em->remove($player);
        $em->flush();

        $this->addFlash('success', 'Joueur supprimé avec succès.');

        return $this->redirectToRoute('admin_players_index');
    }

}

<?php

namespace App\Controller\Admin;

use App\Entity\Registration;
use App\Form\AdminRegistrationType;
use App\Repository\RegistrationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/registrations')]
class AdminRegistrationController extends AbstractController
{
    #[Route('/', name: 'admin_registrations_index')]
    public function index(RegistrationRepository $repo): Response
    {
        return $this->render('admin/registration/index.html.twig', [
            'registrations' => $repo->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin_registrations_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $registration = new Registration();
        $registration->setRegistrationDate(new \DateTime());

        $form = $this->createForm(AdminRegistrationType::class, $registration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($registration);
            $em->flush();

            $this->addFlash('success', 'Inscription ajoutée.');
            return $this->redirectToRoute('admin_registrations_index');
        }

        return $this->render('admin/registration/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_registrations_edit')]
    public function edit(Request $request, Registration $registration, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(AdminRegistrationType::class, $registration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Inscription modifiée.');
            return $this->redirectToRoute('admin_registrations_index');
        }

        return $this->render('admin/registration/edit.html.twig', [
            'form' => $form->createView(),
            'registration' => $registration,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_registrations_delete')]
    public function delete(Registration $registration, EntityManagerInterface $em): Response
    {
        $em->remove($registration);
        $em->flush();

        $this->addFlash('success', 'Inscription supprimée.');
        return $this->redirectToRoute('admin_registrations_index');
    }
}

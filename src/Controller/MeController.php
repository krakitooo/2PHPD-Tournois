<?php 

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MeController extends AbstractController
{
    #[Route('/me', name: 'profile_me')]
    public function __invoke(): Response
    {
        // si l'utilisateur n'est pas connecté -> 401 (géré par le firewall)
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // si l'utilisateur n'est pas un joueur -> 403 (géré par le firewall)
        $this->denyAccessUnlessGranted('ROLE_USER');

        // si l'utilisateur n'est pas un admin -> 403 (géré par le firewall)
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        return $this->render('me.html.twig');
    }
}
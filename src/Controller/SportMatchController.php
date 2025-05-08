<?php

namespace App\Controller;

use App\Entity\SportMatch;
use App\Entity\Tournament;
use App\Entity\User;
use App\Entity\Registration;
use App\Event\NotificationEvent;
use App\Repository\SportMatchRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

#[Route('/api/tournaments')]
class SportMatchController extends AbstractController
{
    #[Route('/{id}/sport-matchs', name: 'match_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(Tournament $id, SportMatchRepository $repo, SerializerInterface $serializer): JsonResponse
    {
        $matches = $repo->findBy(['tournament' => $id]);
        $json = $serializer->serialize($matches, 'json', ['groups' => 'match:read']);
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}/sport-matchs', name: 'match_create', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function create(Tournament $id, Request $request, EntityManagerInterface $em, SerializerInterface $serializer): JsonResponse
    {
        $currentUser = $this->getUser();
        if ($id->getOrganizer() !== $currentUser && !in_array('ROLE_ADMIN', $currentUser->getRoles())) {
            return $this->json(['message' => 'Access denied.'], Response::HTTP_FORBIDDEN);
        }
    
        $data = json_decode($request->getContent(), true);
    
        $player1 = $em->getRepository(User::class)->find($data['player1_id'] ?? null);
        $player2 = $em->getRepository(User::class)->find($data['player2_id'] ?? null);
    
        if (!$player1 || !$player2) {
            return new JsonResponse(['error' => 'Player not found'], Response::HTTP_NOT_FOUND);
        }
    
        $registrationRepo = $em->getRepository(Registration::class);
        $reg1 = $registrationRepo->findOneBy(['player' => $player1, 'tournament' => $id, 'status' => 'confirmée']);
        $reg2 = $registrationRepo->findOneBy(['player' => $player2, 'tournament' => $id, 'status' => 'confirmée']);
    
        if (!$reg1 || !$reg2) {
            return $this->json(['message' => 'Both players must be confirmed participants of the tournament.'], Response::HTTP_BAD_REQUEST);
        }
    
        $match = new SportMatch();
        $match->setTournament($id);
        $match->setPlayer1($player1);
        $match->setPlayer2($player2);
        if (!empty($data['matchDate'])) {
            try {
                $date = new \DateTime($data['matchDate']);
                $match->setMatchDate($date);
            } catch (\Exception $e) {
                return new JsonResponse(['error' => 'Invalid date format'], Response::HTTP_BAD_REQUEST);
            }
        } else {
            $match->setMatchDate(new \DateTime());
        }
        $match->setScorePlayer1(null);
        $match->setScorePlayer2(null);
        $match->setStatus('en attente');
    
        $em->persist($match);
        $em->flush();
    
        $json = $serializer->serialize($match, 'json', ['groups' => 'match:read']);
        return new JsonResponse($json, Response::HTTP_CREATED, [], true);
    }
    
    

    #[Route('/{idTournament}/sport-matchs/{idSportMatchs}', name: 'match_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(SportMatch $idSportMatchs, SerializerInterface $serializer): JsonResponse
    {
        $json = $serializer->serialize($idSportMatchs, 'json', ['groups' => 'match:read']);
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route('/{idTournament}/sport-matchs/{idSportMatchs}', name: 'match_update', methods: ['PUT'])]
    #[IsGranted('ROLE_USER')]
    public function update(Request $request, SportMatch $idSportMatchs, EntityManagerInterface $em, SerializerInterface $serializer, EventDispatcherInterface $dispatcher ): JsonResponse 
    {
        $currentUser = $this->getUser();

        if (!$currentUser instanceof \App\Entity\User) {
            throw new \LogicException('Current user is not an instance of App\Entity\User.');
        }

        $tournament = $idSportMatchs->getTournament();
    
        if (!in_array('ROLE_ADMIN', $currentUser->getRoles()) &&
            $idSportMatchs->getPlayer1() !== $currentUser &&
            $idSportMatchs->getPlayer2() !== $currentUser &&
            $tournament->getOrganizer() !== $currentUser) {
            return $this->json(['message' => 'Access denied.'], Response::HTTP_FORBIDDEN);
        }
    
        $data = json_decode($request->getContent(), true);
    
        if (isset($data['scorePlayer1']) && ($idSportMatchs->getPlayer1() === $currentUser || in_array('ROLE_ADMIN', $currentUser->getRoles()) || $tournament->getOrganizer() === $currentUser)) {
            $idSportMatchs->setScorePlayer1($data['scorePlayer1']);
        }
    
        if (isset($data['scorePlayer2']) && ($idSportMatchs->getPlayer2() === $currentUser || in_array('ROLE_ADMIN', $currentUser->getRoles()) || $tournament->getOrganizer() === $currentUser)) {
            $idSportMatchs->setScorePlayer2($data['scorePlayer2']);
        }
    
        if (!in_array('ROLE_ADMIN', $currentUser->getRoles()) && $tournament->getOrganizer()->getId() !== $currentUser->getId()) {
            if ($currentUser->getId() === $idSportMatchs->getPlayer1()->getId() && $idSportMatchs->getScorePlayer2() === null) {
                $dispatcher->dispatch(new NotificationEvent(
                    $idSportMatchs->getPlayer2(),
                    sprintf(
                        "Ton adversaire %s %s a saisi son score pour le match du tournoi '%s'. À toi de remplir le tien !",
                        $currentUser->getFirstName(),
                        $currentUser->getLastName(),
                        $tournament->getTournamentName()
                    )
                ));
            }
        
            if ($currentUser->getId() === $idSportMatchs->getPlayer2()->getId() && $idSportMatchs->getScorePlayer1() === null) {
                $dispatcher->dispatch(new NotificationEvent(
                    $idSportMatchs->getPlayer1(),
                    sprintf(
                        "Ton adversaire %s %s a saisi son score pour le match du tournoi '%s'. À toi de remplir le tien !",
                        $currentUser->getFirstName(),
                        $currentUser->getLastName(),
                        $tournament->getTournamentName()
                    )
                ));
            }
        }        

        if ($idSportMatchs->getScorePlayer1() !== null && $idSportMatchs->getScorePlayer2() !== null) {
            $idSportMatchs->setStatus('terminé');
        }
    
        $em->flush();
    
        $json = $serializer->serialize($idSportMatchs, 'json', ['groups' => 'match:read']);
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route('/{idTournament}/sport-matchs/{idSportMatchs}', name: 'match_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_USER')]
    public function delete(SportMatch $idSportMatchs, EntityManagerInterface $em): JsonResponse
    {
        $currentUser = $this->getUser();
        $tournament = $idSportMatchs->getTournament();
    
        if ($tournament->getOrganizer() !== $currentUser && !in_array('ROLE_ADMIN', $currentUser->getRoles())) {
            return $this->json(['message' => 'Access denied.'], Response::HTTP_FORBIDDEN);
        }
    
        $em->remove($idSportMatchs);
        $em->flush();
    
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}

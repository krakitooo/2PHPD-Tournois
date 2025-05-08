<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Tournament;
use App\Entity\SportMatch;
use App\Entity\Registration;
use App\Entity\Notification;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;
    
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    
    public function load(ObjectManager $manager): void
    {
        $users = [];
        
        // 5 users
        for ($i = 1; $i <= 5; $i++) {
            $user = new User();
            $user->setUsername("player$i");
            $user->setFirstName("Prenom$i");
            $user->setLastName("Nom$i");
            $user->setEmailAddress("player$i@example.com");
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->passwordHasher->hashPassword($user, "pass$i"));
            $user->setStatus('actif');
            $manager->persist($user);
            $users[] = $user;
        }

        // Admin
        $admin = new User();
        $admin->setUsername('admin');
        $admin->setFirstName('Admin');
        $admin->setLastName('Istrator');
        $admin->setEmailAddress('admin@example.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'adminpass'));
        $admin->setStatus('actif');
        $manager->persist($admin);
        
        $tournaments = [];
        // 2 tournois
        for ($j = 1; $j <= 2; $j++) {
            $tournament = new Tournament();
            $tournament->setTournamentName("Tournoi$j");
            $tournament->setStartDate(new \DateTime("+$j days"));
            $tournament->setEndDate(new \DateTime("+".($j+3)." days"));
            $tournament->setLocation("Lieu$j");
            $tournament->setDescription("Description tournoi $j");
            $tournament->setMaxParticipants(8);
            $tournament->setSport('Football');
            $tournament->setOrganizer($admin);
            $manager->persist($tournament);
            $tournaments[] = $tournament;
        }

        $registrations = [];
        // Inscrire chaque joueur aux 2 tournois
        foreach ($users as $user) {
            foreach ($tournaments as $tournament) {
                $registration = new Registration();
                $registration->setPlayer($user);
                $registration->setTournament($tournament);
                $registration->setRegistrationDate(new \DateTime());
                $registration->setStatus('confirmée');
                $manager->persist($registration);
                $registrations[] = $registration;
            }
        }

        // Créer des matchs avec scores prédéfinis
        $match1 = new SportMatch();
        $match1->setTournament($tournaments[0]);
        $match1->setPlayer1($users[0]);
        $match1->setPlayer2($users[1]);
        $match1->setMatchDate(new \DateTime("+2 days"));
        $match1->setScorePlayer1(3); // player1 gagne
        $match1->setScorePlayer2(1);
        $match1->setStatus('terminé');
        $manager->persist($match1);

        $match2 = new SportMatch();
        $match2->setTournament($tournaments[0]);
        $match2->setPlayer1($users[2]);
        $match2->setPlayer2($users[3]);
        $match2->setMatchDate(new \DateTime("+2 days"));
        $match2->setScorePlayer1(0); // égalité
        $match2->setScorePlayer2(0);
        $match2->setStatus('terminé');
        $manager->persist($match2);

        $match3 = new SportMatch();
        $match3->setTournament($tournaments[1]);
        $match3->setPlayer1($users[1]);
        $match3->setPlayer2($users[4]);
        $match3->setMatchDate(new \DateTime("+3 days"));
        $match3->setScorePlayer1(1); // player2 gagne
        $match3->setScorePlayer2(2);
        $match3->setStatus('terminé');
        $manager->persist($match3);

        $manager->flush();
    }
}

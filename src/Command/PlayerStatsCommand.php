<?php

namespace App\Command;

use App\Entity\User;
use App\Entity\SportMatch;
use App\Entity\Tournament;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Command\Command;

#[AsCommand(
    name: 'app:player:stats',
    description: 'Affiche le nombre de victoires et de défaites pour un joueur (avec option tournoi).',
)]
class PlayerStatsCommand extends Command
{
    private EntityManagerInterface $em;
    
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }
    
    protected function configure(): void
    {
        $this
            ->addArgument('userId', InputArgument::REQUIRED, 'ID du joueur')
            ->addArgument('tournamentId', InputArgument::OPTIONAL, 'ID du tournoi (optionnel)');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $userId = $input->getArgument('userId');
        $tournamentId = $input->getArgument('tournamentId');

        /** @var User $user */
        $user = $this->em->getRepository(User::class)->find($userId);
        if (!$user) {
            $output->writeln('<error>Joueur non trouvé</error>');
            return Command::FAILURE;
        }

        // Construire la requête
        $qb = $this->em->getRepository(SportMatch::class)->createQueryBuilder('m')
            ->where('m.player1 = :user OR m.player2 = :user')
            ->setParameter('user', $user);
        
        if ($tournamentId) {
            $qb->andWhere('m.tournament = :tournament')
               ->setParameter('tournament', $tournamentId);
        }

        $matches = $qb->getQuery()->getResult();

        $victories = 0;
        $defeats = 0;
        $draws = 0;

        foreach ($matches as $match) {
            if ($match->getStatus() !== 'terminé') {
                continue; // ignorer les matchs non terminés
            }

            $isPlayer1 = $match->getPlayer1()->getId() === $user->getId();
            $scorePlayer = $isPlayer1 ? $match->getScorePlayer1() : $match->getScorePlayer2();
            $scoreOpponent = $isPlayer1 ? $match->getScorePlayer2() : $match->getScorePlayer1();

            if ($scorePlayer > $scoreOpponent) {
                $victories++;
            } elseif ($scorePlayer < $scoreOpponent) {
                $defeats++;
            } else {
                $draws++;
            }
        }

        $output->writeln("Statistiques de {$user->getFirstName()} {$user->getLastName()} (ID {$userId}):");
        $output->writeln("Victoires : $victories");
        $output->writeln("Défaites : $defeats");
        $output->writeln("Matchs nuls : $draws");

        if ($tournamentId) {
            $output->writeln("Tournoi filtré : ID $tournamentId");
        }

        return Command::SUCCESS;
    }
}

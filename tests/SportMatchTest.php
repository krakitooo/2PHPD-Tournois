<?php

namespace App\Tests;

use App\Entity\SportMatch;
use App\Entity\Tournament;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class SportMatchTest extends TestCase
{
    public function testSportMatchGettersAndSetters(): void
    {
        $match = new SportMatch();
        $tournament = new Tournament();
        $player1 = new User();
        $player2 = new User();
        $date = new \DateTime('2025-06-01');

        $match->setTournament($tournament);
        $match->setPlayer1($player1);
        $match->setPlayer2($player2);
        $match->setMatchDate($date);
        $match->setScorePlayer1(2);
        $match->setScorePlayer2(1);
        $match->setStatus('en cours');

        $this->assertSame($tournament, $match->getTournament());
        $this->assertSame($player1, $match->getPlayer1());
        $this->assertSame($player2, $match->getPlayer2());
        $this->assertEquals($date, $match->getMatchDate());
        $this->assertEquals(2, $match->getScorePlayer1());
        $this->assertEquals(1, $match->getScorePlayer2());
        $this->assertEquals('en cours', $match->getStatus());
    }

    public function testStatusShouldChangeToTermineWhenScoresAreSet(): void
    {
        $match = new SportMatch();
        $match->setScorePlayer1(1);
        $match->setScorePlayer2(2);

        if ($match->getScorePlayer1() !== null && $match->getScorePlayer2() !== null) {
            $match->setStatus('terminé');
        }

        $this->assertEquals('terminé', $match->getStatus());
    }
}

<?php

namespace App\Tests;

use App\Entity\Tournament;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class TournamentTest extends TestCase
{
    public function testTournamentGettersAndSetters(): void
    {
        $tournament = new Tournament();

        $organizer = new User();

        $tournament->setTournamentName('Tournoi de Paris');
        $tournament->setStartDate(new \DateTime('2025-06-01'));
        $tournament->setEndDate(new \DateTime('2025-06-10'));
        $tournament->setLocation('Paris');
        $tournament->setDescription('Un grand tournoi');
        $tournament->setMaxParticipants(16);
        $tournament->setSport('Football');
        $tournament->setOrganizer($organizer);
        $tournament->setWinner($organizer);

        $this->assertEquals('Tournoi de Paris', $tournament->getTournamentName());
        $this->assertEquals('Paris', $tournament->getLocation());
        $this->assertEquals('Un grand tournoi', $tournament->getDescription());
        $this->assertEquals(16, $tournament->getMaxParticipants());
        $this->assertEquals('Football', $tournament->getSport());
        $this->assertSame($organizer, $tournament->getOrganizer());
        $this->assertSame($organizer, $tournament->getWinner());
        $this->assertInstanceOf(\DateTime::class, $tournament->getStartDate());
        $this->assertInstanceOf(\DateTime::class, $tournament->getEndDate());
    }

    public function testStatusBasedOnDates(): void
    {
        $tournament = new Tournament();
        $tournament->setStartDate(new \DateTime('-1 day'));
        $tournament->setEndDate(new \DateTime('+1 day'));

        $today = new \DateTime();

        if ($today < $tournament->getStartDate()) {
            $expectedStatus = 'en attente';
        } elseif ($today > $tournament->getEndDate()) {
            $expectedStatus = 'terminé';
        } else {
            $expectedStatus = 'en cours';
        }

        $this->assertEquals($expectedStatus, $expectedStatus);
    }
}

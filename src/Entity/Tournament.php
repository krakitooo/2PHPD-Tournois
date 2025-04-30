<?php

namespace App\Entity;

use App\Repository\TournamentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TournamentRepository::class)]
class Tournament
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $tournamentName = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $startDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $endDate = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $location = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $maxParticipants = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column(length: 255)]
    private ?string $sport = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'tournaments')]
    private Collection $organizer;

    #[ORM\ManyToOne]
    private ?User $winner = null;

    /**
     * @var Collection<int, SportMatch>
     */
    #[ORM\OneToMany(targetEntity: SportMatch::class, mappedBy: 'tournament')]
    private Collection $sportMatches;

    public function __construct()
    {
        $this->organizer = new ArrayCollection();
        $this->sportMatches = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTournamentName(): ?string
    {
        return $this->tournamentName;
    }

    public function setTournamentName(string $tournamentName): static
    {
        $this->tournamentName = $tournamentName;

        return $this;
    }

    public function getStartDate(): ?\DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTime $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTime $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getMaxParticipants(): ?int
    {
        return $this->maxParticipants;
    }

    public function setMaxParticipants(int $maxParticipants): static
    {
        $this->maxParticipants = $maxParticipants;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getSport(): ?string
    {
        return $this->sport;
    }

    public function setSport(string $sport): static
    {
        $this->sport = $sport;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getOrganizer(): Collection
    {
        return $this->organizer;
    }

    public function addOrganizer(User $organizer): static
    {
        if (!$this->organizer->contains($organizer)) {
            $this->organizer->add($organizer);
        }

        return $this;
    }

    public function removeOrganizer(User $organizer): static
    {
        $this->organizer->removeElement($organizer);

        return $this;
    }

    public function getWinner(): ?User
    {
        return $this->winner;
    }

    public function setWinner(?User $winner): static
    {
        $this->winner = $winner;

        return $this;
    }

    /**
     * @return Collection<int, SportMatch>
     */
    public function getSportMatches(): Collection
    {
        return $this->sportMatches;
    }

    public function addSportMatch(SportMatch $sportMatch): static
    {
        if (!$this->sportMatches->contains($sportMatch)) {
            $this->sportMatches->add($sportMatch);
            $sportMatch->setTournament($this);
        }

        return $this;
    }

    public function removeSportMatch(SportMatch $sportMatch): static
    {
        if ($this->sportMatches->removeElement($sportMatch)) {
            // set the owning side to null (unless already changed)
            if ($sportMatch->getTournament() === $this) {
                $sportMatch->setTournament(null);
            }
        }

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\UuidV4;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $firstName = null;

    #[ORM\Column(length: 50)]
    private ?string $lastName = null;

    #[ORM\Column]
    private ?int $vacationDays = null;

    #[ORM\Column]
    private ?int $compensatoryTimeDays = null;

    #[ORM\OneToMany(mappedBy: 'employee', targetEntity: OffTime::class)]
    private Collection $offTimes;

    public function __construct()
    {
        $this->offTimes = new ArrayCollection();
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getVacationDays(): ?int
    {
        return $this->vacationDays;
    }

    public function setVacationDays(int $vacationDays): self
    {
        $this->vacationDays = $vacationDays;

        return $this;
    }

    public function getCompensatoryTimeDays(): ?int
    {
        return $this->compensatoryTimeDays;
    }

    public function setCompensatoryTimeDays(int $compensatoryTimeDays): self
    {
        $this->compensatoryTimeDays = $compensatoryTimeDays;

        return $this;
    }

    /**
     * @return Collection<int, OffTime>
     */
    public function getOffTimes(): Collection
    {
        return $this->offTimes;
    }

    public function addOffTime(OffTime $offTime): self
    {
        if (!$this->offTimes->contains($offTime)) {
            $this->offTimes->add($offTime);
            $offTime->setEmployee($this);
        }

        return $this;
    }

    public function removeOffTime(OffTime $offTime): self
    {
        if ($this->offTimes->removeElement($offTime)) {
            // set the owning side to null (unless already changed)
            if ($offTime->getEmployee() === $this) {
                $offTime->setEmployee(null);
            }
        }

        return $this;
    }
}

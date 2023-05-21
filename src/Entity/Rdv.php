<?php

namespace App\Entity;

use App\Repository\RdvRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RdvRepository::class)]
class Rdv
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIMETZ_MUTABLE)]
    private ?\DateTimeInterface $dat = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDat(): ?\DateTimeInterface
    {
        return $this->dat;
    }

    public function setDat(\DateTimeInterface $dat): self
    {
        $this->dat = $dat;

        return $this;
    }
}

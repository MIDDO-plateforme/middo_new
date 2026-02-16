<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Escrow
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private int $amount;

    #[ORM\Column(length: 255)]
    private string $projectId;

    #[ORM\Column(length: 20)]
    private string $status;

    #[ORM\Column(length: 255)]
    private string $txHash;

    #[ORM\Column(length: 255)]
    private string $contractAddress;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $lockedUntil;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $releaseTxHash = null;

    // Getters & setters...
}

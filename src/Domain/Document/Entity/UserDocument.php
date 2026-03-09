<?php

namespace App\Domain\Document\Entity;

use App\Domain\User\Entity\User;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "user_document")]
class UserDocument
{
    #[ORM\Id]
    #[ORM\Column(type: "string", length: 36)]
    private string $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $owner;

    #[ORM\Column(type: "string", length: 255)]
    private string $filename;

    #[ORM\Column(type: "string", length: 255)]
    private string $originalName;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $uploadedAt;

    public function __construct(string $id, User $owner, string $filename, string $originalName)
    {
        $this->id = $id;
        $this->owner = $owner;
        $this->filename = $filename;
        $this->originalName = $originalName;
        $this->uploadedAt = new \DateTimeImmutable();
    }

    public function id(): string { return $this->id; }
    public function owner(): User { return $this->owner; }
    public function filename(): string { return $this->filename; }
    public function originalName(): string { return $this->originalName; }
    public function uploadedAt(): \DateTimeInterface { return $this->uploadedAt; }
}

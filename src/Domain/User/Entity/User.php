<?php

namespace App\Domain\User\Entity;

use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\PasswordHash;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity]
#[ORM\Table(name: "user")]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: "string", length: 36)]
    private string $id;

    #[ORM\Column(name: "email", type: "string", length: 255, unique: true)]
    private string $emailValue;

    #[ORM\Column(name: "password", type: "string", length: 255)]
    private string $passwordValue;

    #[ORM\Column(name: "ia_settings", type: "json", nullable: true)]
    private array $iaSettings = [];

    public function __construct(
        string $id,
        Email $email,
        PasswordHash $passwordHash,
        array $iaSettings = []
    ) {
        $this->id = $id;
        $this->emailValue = $email->value();
        $this->passwordValue = $passwordHash->value();
        $this->iaSettings = $iaSettings;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function email(): Email
    {
        return new Email($this->emailValue);
    }

    public function passwordHash(): PasswordHash
    {
        return new PasswordHash($this->passwordValue);
    }

    public function iaSettings(): array
    {
        return $this->iaSettings ?? [];
    }

    public function setIaSettings(array $settings): void
    {
        $this->iaSettings = $settings;
    }

    public function getUserIdentifier(): string
    {
        return $this->emailValue;
    }

    public function getPassword(): string
    {
        return $this->passwordValue;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function eraseCredentials(): void {}
}

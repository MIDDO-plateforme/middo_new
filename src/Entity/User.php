<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @var Collection<int, Notification>
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Notification::class, orphanRemoval: true)]
    private Collection $notifications;
    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Workspace::class)]
    private Collection $workspaces;

    #[ORM\ManyToMany(targetEntity: Workspace::class, mappedBy: 'members')]
    private Collection $memberWorkspaces;

    public function __construct()
    {
        $this->workspaces = new ArrayCollection();
        $this->memberWorkspaces = new ArrayCollection();
        $this->notifications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void
    {
        // Clear temporary sensitive data if needed
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): static
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->setUser($this);
        }
        return $this;
    }

    public function removeNotification(Notification $notification): static
    {
        if ($this->notifications->removeElement($notification)) {
            if ($notification->getUser() === $this) {
                $notification->setUser(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Workspace>
     */
    public function getWorkspaces(): Collection
    {
        return $this->workspaces;
    }

    public function addWorkspace(Workspace $workspace): static
    {
        if (!$this->workspaces->contains($workspace)) {
            $this->workspaces->add($workspace);
            $workspace->setOwner($this);
        }
        return $this;
    }

    public function removeWorkspace(Workspace $workspace): static
    {
        if ($this->workspaces->removeElement($workspace)) {
            if ($workspace->getOwner() === $this) {
                $workspace->setOwner(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Workspace>
     */
    public function getMemberWorkspaces(): Collection
    {
        return $this->memberWorkspaces;
    }

    public function addMemberWorkspace(Workspace $memberWorkspace): static
    {
        if (!$this->memberWorkspaces->contains($memberWorkspace)) {
            $this->memberWorkspaces->add($memberWorkspace);
            $memberWorkspace->addMember($this);
        }
        return $this;
    }

    public function removeMemberWorkspace(Workspace $memberWorkspace): static
    {
        if ($this->memberWorkspaces->removeElement($memberWorkspace)) {
            $memberWorkspace->removeMember($this);
        }
        return $this;
    }
}

<?php

namespace App\Domain\Auth\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'refresh_tokens')]
class RefreshTokenEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    public string $token;

    #[ORM\Column(type: 'string', length: 36)]
    public string $userId;

    #[ORM\Column(type: 'string', length: 64)]
    public string $sessionId;

    #[ORM\Column(type: 'string', length: 64)]
    public string $deviceId;

    #[ORM\Column(type: 'datetime_immutable')]
    public \DateTimeImmutable $expiresAt;

    public function __construct(
        string $userId,
        string $sessionId,
        string $deviceId,
        string $token,
        \DateTimeImmutable $expiresAt
    ) {
        $this->userId = $userId;
        $this->sessionId = $sessionId;
        $this->deviceId = $deviceId;
        $this->token = $token;
        $this->expiresAt = $expiresAt;
    }
}

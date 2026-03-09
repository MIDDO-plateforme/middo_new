<?php

namespace App\Infrastructure\Auth\Doctrine;

use App\Domain\Auth\Service\RefreshTokenStoreInterface;
use App\Domain\Auth\Entity\RefreshTokenEntity;
use App\Domain\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class RefreshTokenStoreDoctrine implements RefreshTokenStoreInterface
{
    public function __construct(private EntityManagerInterface $em) {}

    public function create(User $user): RefreshTokenEntity
    {
        $token = new RefreshTokenEntity(
            userId: $user->id(),
            sessionId: bin2hex(random_bytes(16)),
            deviceId: 'default-device',
            token: bin2hex(random_bytes(32)),
            expiresAt: new \DateTimeImmutable('+30 days')
        );

        $this->em->persist($token);
        $this->em->flush();

        return $token;
    }

    public function find(string $id): ?RefreshTokenEntity
    {
        return $this->em->getRepository(RefreshTokenEntity::class)->find($id);
    }

    public function rotate(RefreshTokenEntity $oldToken): RefreshTokenEntity
    {
        $new = new RefreshTokenEntity(
            userId: $oldToken->userId,
            sessionId: $oldToken->sessionId,
            deviceId: $oldToken->deviceId,
            token: bin2hex(random_bytes(32)),
            expiresAt: new \DateTimeImmutable('+30 days')
        );

        $this->em->remove($oldToken);
        $this->em->persist($new);
        $this->em->flush();

        return $new;
    }

    public function remove(RefreshTokenEntity $token): void
    {
        $this->em->remove($token);
        $this->em->flush();
    }
}

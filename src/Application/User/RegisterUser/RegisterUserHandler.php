<?php

namespace App\Application\User\RegisterUser;

use App\Domain\User\Entity\User;
use App\Domain\User\Exception\UserAlreadyExists;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\PasswordHash;
use App\Domain\User\ValueObject\UserPreferences;
use App\Domain\User\ValueObject\IaSettings;
use App\Domain\User\Event\UserRegistered;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\Service\PasswordHasherInterface;
use Ramsey\Uuid\Uuid;

class RegisterUserHandler
{
    public function __construct(
        private UserRepositoryInterface $users,
        private PasswordHasherInterface $hasher,
    ) {}

    public function handle(RegisterUserCommand $command): User
    {
        $email = new Email($command->email);

        if ($this->users->existsByEmail($email)) {
            throw new UserAlreadyExists($email->value());
        }

        $id = Uuid::uuid4()->toString();
        $passwordHash = new PasswordHash(
            $this->hasher->hash($command->password)
        );

        $user = new User(
            id: $id,
            email: $email,
            passwordHash: $passwordHash,
            roles: ['ROLE_USER'],
            firstname: $command->firstname,
            lastname: $command->lastname,
            avatar: null,
            locale: $command->locale,
            timezone: $command->timezone,
            preferences: new UserPreferences(),
            iaSettings: new IaSettings()
        );

        $this->users->save($user);

        // Event (future dispatcher)
        $event = new UserRegistered($id, $email->value());

        return $user;
    }
}

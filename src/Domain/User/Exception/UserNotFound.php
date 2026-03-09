<?php

namespace App\Domain\User\Exception;

use DomainException;

class UserNotFound extends DomainException
{
    public function __construct(string $idOrEmail)
    {
        parent::__construct("User not found: $idOrEmail");
    }
}

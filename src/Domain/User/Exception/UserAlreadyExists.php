<?php

namespace App\Domain\User\Exception;

use DomainException;

class UserAlreadyExists extends DomainException
{
    public function __construct(string $email)
    {
        parent::__construct("A user already exists with email: $email");
    }
}

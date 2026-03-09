<?php

namespace App\Domain\User\Exception;

use DomainException;

class InvalidEmail extends DomainException
{
    public function __construct(string $email)
    {
        parent::__construct("Invalid email: $email");
    }
}

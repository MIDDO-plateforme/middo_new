<?php

namespace App\Domain\User\ValueObject;

use Symfony\Component\Uid\Uuid;

class UserId
{
    public static function generate(): string
    {
        return Uuid::v4()->toRfc4122();
    }
}

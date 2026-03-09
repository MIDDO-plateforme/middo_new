<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class OwnerVoter extends Voter
{
    public const OWNED = 'ENTITY_OWNED';

    protected function supports(string $attribute, $subject): bool
    {
        return $attribute === self::OWNED && method_exists($subject, 'owner');
    }

    protected function voteOnAttribute(string $attribute, $entity, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user) {
            return false;
        }

        return $entity->owner()->id() === $user->id();
    }
}

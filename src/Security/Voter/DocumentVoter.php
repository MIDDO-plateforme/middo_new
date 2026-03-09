<?php

namespace App\Security\Voter;

use App\Domain\Document\Entity\UserDocument;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class DocumentVoter extends Voter
{
    public const VIEW = 'DOCUMENT_VIEW';

    protected function supports(string $attribute, $subject): bool
    {
        return $attribute === self::VIEW && $subject instanceof UserDocument;
    }

    protected function voteOnAttribute(string $attribute, $document, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user) {
            return false;
        }

        return $document->owner()->id() === $user->id();
    }
}

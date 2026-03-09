<?php

namespace App\Security\Voter;

use App\Domain\Notification\Entity\UserNotification;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class NotificationVoter extends Voter
{
    public const VIEW = 'NOTIFICATION_VIEW';

    protected function supports(string $attribute, $subject): bool
    {
        return $attribute === self::VIEW && $subject instanceof UserNotification;
    }

    protected function voteOnAttribute(string $attribute, $notification, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user) {
            return false;
        }

        return $notification->user()->id() === $user->id();
    }
}

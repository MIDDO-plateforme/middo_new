<?php

namespace App\Twig;

use App\Entity\User;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ProfilePictureExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('profile_picture', [$this, 'getProfilePicture']),
            new TwigFunction('user_initials', [$this, 'getUserInitials']),
        ];
    }

    /**
     * Retourne l'URL de la photo de profil ou null
     */
    public function getProfilePicture(?User $user): ?string
    {
        if (!$user) {
            return null;
        }

        $profilePicture = $user->getProfilePicture();
        
        if ($profilePicture) {
            return '/uploads/profiles/' . $profilePicture;
        }

        return null;
    }

    /**
     * Retourne les initiales de l'utilisateur (fallback)
     */
    public function getUserInitials(?User $user): string
    {
        if (!$user) {
            return '?';
        }

        $firstName = $user->getFirstName() ?? '';
        $lastName = $user->getLastName() ?? '';

        $initials = '';
        if ($firstName) {
            $initials .= mb_substr($firstName, 0, 1);
        }
        if ($lastName) {
            $initials .= mb_substr($lastName, 0, 1);
        }

        return $initials ?: '?';
    }
}

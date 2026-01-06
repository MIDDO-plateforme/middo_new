<?php

namespace App\Security\Voter;

use App\Entity\Project;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ProjectVoter extends Voter
{
    public const VIEW = 'view';
    public const EDIT = 'edit';
    public const DELETE = 'delete';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])
            && $subject instanceof Project;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var Project $project */
        $project = $subject;

        return match($attribute) {
            self::VIEW => $this->canView($project, $user),
            self::EDIT => $this->canEdit($project, $user),
            self::DELETE => $this->canDelete($project, $user),
            default => false,
        };
    }

    private function canView(Project $project, User $user): bool
    {
        // Le créateur ou un membre peut voir le projet
        return $project->getCreator() === $user || $project->getMembers()->contains($user);
    }

    private function canEdit(Project $project, User $user): bool
    {
        // Seul le créateur peut modifier
        return $project->getCreator() === $user;
    }

    private function canDelete(Project $project, User $user): bool
    {
        // Seul le créateur peut supprimer
        return $project->getCreator() === $user;
    }
}
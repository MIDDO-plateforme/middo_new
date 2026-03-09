<?php

namespace App\IA\History;

use App\Domain\IA\Entity\IaInteraction;
use App\Domain\User\Entity\User;
use App\Infrastructure\IA\Repository\IaInteractionRepository;

class IaHistoryLogger
{
    public function __construct(private IaInteractionRepository $repository)
    {
    }

    public function log(
        ?User $user,
        string $prompt,
        ?string $answer,
        string $provider,
        bool $success,
        float $durationMs
    ): void {
        $interaction = new IaInteraction();
        $interaction
            ->setUser($user)
            ->setPrompt($prompt)
            ->setAnswer($answer)
            ->setProvider($provider)
            ->setSuccess($success)
            ->setDurationMs($durationMs);

        $this->repository->save($interaction);
    }
}

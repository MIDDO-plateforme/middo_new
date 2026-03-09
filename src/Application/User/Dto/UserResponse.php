<?php

namespace App\Application\User\Dto;

use App\Domain\User\Entity\User;

class UserResponse
{
    public string $id;
    public string $email;
    public array $iaSettings;

    public function __construct(User $user)
    {
        $this->id = $user->id;
        $this->email = $user->email->value();
        $this->iaSettings = [
            'tone' => $user->iaSettings->tone,
            'temperature' => $user->iaSettings->temperature,
            'maxTokens' => $user->iaSettings->maxTokens,
        ];
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'iaSettings' => $this->iaSettings,
        ];
    }
}

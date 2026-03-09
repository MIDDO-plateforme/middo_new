<?php

namespace App\IA\Agent;

interface IaAgentInterface
{
    public function getName(): string;

    public function supports(string $task): bool;

    public function process(string $task, string $input): string;
}

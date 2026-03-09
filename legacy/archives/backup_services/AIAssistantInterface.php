<?php

namespace App\Service;

interface AIAssistantInterface
{
    public function generateResponse(string $prompt, ?array $context = null): string;
    
    public function suggestProjectImprovements(string $projectDescription, array $projectData): string;
    
    public function matchUsersToProject(string $projectDescription, array $projectData): string;
    
    public function analyzeSentiment(string $text): array;
}
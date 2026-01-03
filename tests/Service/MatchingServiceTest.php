<?php

namespace App\Tests\Service;

use App\Service\MatchingService;
use PHPUnit\Framework\TestCase;

class MatchingServiceTest extends TestCase
{
    public function testFindMatchingMissions(): void
    {
        $service = new MatchingService();

        $userSkills = ['Symfony', 'PHP'];
        $missions = $service->findMatching($userSkills);

        $this->assertIsArray($missions);
        $this->assertNotEmpty($missions);

        // Verify each mission has required fields
        foreach ($missions as $mission) {
            $this->assertArrayHasKey('id', $mission);
            $this->assertArrayHasKey('title', $mission);
            $this->assertArrayHasKey('score', $mission);
            $this->assertArrayHasKey('reasons', $mission);
            
            // Score should be between 0 and 100
            $this->assertGreaterThanOrEqual(0, $mission['score']);
            $this->assertLessThanOrEqual(100, $mission['score']);
        }
    }

    public function testMatchingScoreCalculation(): void
    {
        $service = new MatchingService();

        $missions = $service->findMatching(['Symfony', 'PHP', 'Docker']);

        // Missions with more matching skills should have higher scores
        $this->assertGreaterThan(0, $missions[0]['score']);
    }
}

<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\User;
use App\Service\AI\MatchingService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints as Assert;

#[Route('/api/matching')]
// #[IsGranted('ROLE_USER')]
class MatchingController extends AbstractController
{
    public function __construct(
        private readonly MatchingService $matchingService,
        private readonly LoggerInterface $logger,
        private readonly RateLimiterFactory $apiMatchingLimiter
    ) {}
    #[Route('/collaborators', name: 'api_matching_collaborators', methods: ['POST'])]
    public function matchCollaborators(
        #[MapRequestPayload] MatchCollaboratorsRequest $request
    ): JsonResponse {
        $currentUser = $this->getUser();
        $limiter = $this->apiMatchingLimiter->create(
            $currentUser ? $currentUser->getUserIdentifier() : 'anonymous'
        );
        
        if (!$limiter->consume(1)->isAccepted()) {
            return $this->json([
                'success' => false,
                'error' => 'Rate limit dépassé. Réessayez dans quelques instants.'
            ], 429);
        }

        try {
            $matches = $this->matchingService->matchCollaboratorsForProject(
                $request->projectId,
                $request->requiredSkills,
                $request->optionalSkills,
                $request->minAvailability,
                $request->maxResults
            );

            return $this->json([
                'success' => true,
                'matches' => $matches,
                'total' => count($matches)
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Erreur matching collaborateurs', [
                'error' => $e->getMessage(),
                'projectId' => $request->projectId
            ]);

            return $this->json([
                'success' => false,
                'error' => 'Erreur lors du matching des collaborateurs'
            ], 500);
        }
    }
    #[Route('/projects', name: 'api_matching_projects', methods: ['POST'])]
    public function suggestProjects(
        #[MapRequestPayload] SuggestProjectsRequest $request
    ): JsonResponse {
        $currentUser = $this->getUser();
        $limiter = $this->apiMatchingLimiter->create(
            $currentUser ? $currentUser->getUserIdentifier() : 'anonymous'
        );
        
        if (!$limiter->consume(1)->isAccepted()) {
            return $this->json([
                'success' => false,
                'error' => 'Rate limit dépassé. Réessayez dans quelques instants.'
            ], 429);
        }

        try {
            $this->logger->info('User authenticated', [
                'user' => $currentUser ? $currentUser->getUserIdentifier() : 'anonymous'
            ]);

            $suggestions = $this->matchingService->suggestProjectsForUser([
                'id' => $request->userId,
                'skills' => $request->userSkills,
                'interests' => $request->interests,
                'availability' => $request->availability,
                'maxResults' => $request->maxResults
            ]);

            return $this->json([
                'success' => true,
                'suggestions' => $suggestions,
                'total' => count($suggestions)
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Erreur suggestions projets', [
                'error' => $e->getMessage(),
                'userId' => $request->userId
            ]);

            return $this->json([
                'success' => false,
                'error' => 'Erreur lors de la suggestion de projets'
            ], 500);
        }
    }
    #[Route('/compatibility', name: 'api_matching_compatibility', methods: ['POST'])]
    public function calculateCompatibility(
        #[MapRequestPayload] CompatibilityRequest $request
    ): JsonResponse {
        $currentUser = $this->getUser();
        $limiter = $this->apiMatchingLimiter->create(
            $currentUser ? $currentUser->getUserIdentifier() : 'anonymous'
        );
        
        if (!$limiter->consume(1)->isAccepted()) {
            return $this->json([
                'success' => false,
                'error' => 'Rate limit dépassé. Réessayez dans quelques instants.'
            ], 429);
        }

        try {
            $score = $this->matchingService->calculateCompatibilityScore(
                $request->userId,
                $request->projectId,
                $request->userSkills,
                $request->projectRequirements
            );

            return $this->json([
                'success' => true,
                'compatibility' => $score
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Erreur calcul compatibilité', [
                'error' => $e->getMessage(),
                'userId' => $request->userId,
                'projectId' => $request->projectId
            ]);

            return $this->json([
                'success' => false,
                'error' => 'Erreur lors du calcul de compatibilité'
            ], 500);
        }
    }

    #[Route('/skill-gaps', name: 'api_matching_skill_gaps', methods: ['POST'])]
    public function identifySkillGaps(
        #[MapRequestPayload] SkillGapsRequest $request
    ): JsonResponse {
        $currentUser = $this->getUser();
        $limiter = $this->apiMatchingLimiter->create(
            $currentUser ? $currentUser->getUserIdentifier() : 'anonymous'
        );
        
        if (!$limiter->consume(1)->isAccepted()) {
            return $this->json([
                'success' => false,
                'error' => 'Rate limit dépassé. Réessayez dans quelques instants.'
            ], 429);
        }

        try {
            $gaps = $this->matchingService->identifySkillGaps(
                $request->userId,
                $request->currentSkills,
                $request->targetRole,
                $request->industryStandards
            );

            return $this->json([
                'success' => true,
                'gaps' => $gaps
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Erreur identification gaps', [
                'error' => $e->getMessage(),
                'userId' => $request->userId
            ]);

            return $this->json([
                'success' => false,
                'error' => 'Erreur lors de l\'identification des gaps'
            ], 500);
        }
    }
}
// ==================== DTOs ====================

class MatchCollaboratorsRequest
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly int $projectId,

        #[Assert\NotBlank]
        #[Assert\Type('array')]
        public readonly array $requiredSkills,

        #[Assert\Type('array')]
        public readonly array $optionalSkills = [],

        #[Assert\Range(min: 0, max: 100)]
        public readonly int $minAvailability = 20,

        #[Assert\Range(min: 1, max: 50)]
        public readonly int $maxResults = 10
    ) {}
}

class SuggestProjectsRequest
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly int $userId,

        #[Assert\NotBlank]
        #[Assert\Type('array')]
        public readonly array $userSkills,

        #[Assert\Type('array')]
        public readonly array $interests = [],

        #[Assert\Range(min: 0, max: 100)]
        public readonly int $availability = 50,

        #[Assert\Range(min: 1, max: 50)]
        public readonly int $maxResults = 10
    ) {}
}

class CompatibilityRequest
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly int $userId,

        #[Assert\NotBlank]
        public readonly int $projectId,

        #[Assert\NotBlank]
        #[Assert\Type('array')]
        public readonly array $userSkills,

        #[Assert\NotBlank]
        #[Assert\Type('array')]
        public readonly array $projectRequirements
    ) {}
}

class SkillGapsRequest
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly int $userId,

        #[Assert\NotBlank]
        #[Assert\Type('array')]
        public readonly array $currentSkills,

        #[Assert\NotBlank]
        public readonly string $targetRole,

        #[Assert\Type('array')]
        public readonly array $industryStandards = []
    ) {}
}

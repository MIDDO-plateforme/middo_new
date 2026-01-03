<?php

namespace App\Trait;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\RateLimiter\RateLimiterFactory;

trait RateLimiterTrait
{
    private function checkRateLimit(
        Request $request,
        RateLimiterFactory $limiter,
        string $apiName
    ): ?JsonResponse {
        // Identifier par IP
        $clientIp = $request->getClientIp();
        
        // Créer limiter pour cette IP
        $limit = $limiter->create($clientIp);
        
        // Consommer 1 token
        $rateLimit = $limit->consume(1);
        
        // Si limite dépassée
        if (!$rateLimit->isAccepted()) {
            $this->logger->warning("🚫 Rate Limit dépassé pour $apiName", [
                'ip' => $clientIp
            ]);
            
            return new JsonResponse([
                'success' => false,
                'error' => 'Trop de requêtes. Veuillez réessayer plus tard.',
                'message' => "Limite dépassée pour $apiName"
            ], 429);
        }
        
        // Limite OK, pas de réponse d'erreur
        return null;
    }
}

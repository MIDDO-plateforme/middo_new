<?php

namespace App\Infrastructure\Http\Middleware;

use Symfony\Component\HttpKernel\Event\RequestEvent;

class DeviceIdMiddleware
{
    public function __invoke(RequestEvent $event): void
    {
        $request = $event->getRequest();

        // Si déjà présent, ne rien faire
        if ($request->headers->has('X-Device-Id')) {
            return;
        }

        // Génère un ID simple si absent
        $deviceId = bin2hex(random_bytes(8));

        // Ajoute dans les headers pour la suite du traitement
        $request->headers->set('X-Device-Id', $deviceId);
    }
}

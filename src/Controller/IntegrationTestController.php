<?php

namespace App\Controller;

use App\Entity\PartnerAction;
use App\Entity\PartnerConnector;
use App\Integrations\IntegrationManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class IntegrationTestController extends AbstractController
{
    #[Route('/api/integration/test', name: 'api_integration_test', methods: ['GET'])]
    public function test(
        EntityManagerInterface $em,
        IntegrationManager $manager
    ): JsonResponse {
        
        // 1. Récupérer un PartnerConnector existant
        $connector = $em->getRepository(PartnerConnector::class)->findOneBy([]);

        if (!$connector) {
            return new JsonResponse([
                'error' => 'Aucun PartnerConnector trouvé. Crée un connecteur avant de tester.'
            ], 400);
        }

        // 2. Créer une action de test
        $action = new PartnerAction();
        $action->setPartnerConnector($connector);
        $action->setActionType('demo_test');
        $action->setParameters(['test' => true]);

        $em->persist($action);
        $em->flush();

        // 3. Exécuter l’action via IntegrationManager
        $manager->executeAction($action);

        return new JsonResponse([
            'status' => $action->getStatus(),
            'result' => $action->getResult(),
            'executedAt' => $action->getExecutedAt()?->format('Y-m-d H:i:s')
        ]);
    }
}

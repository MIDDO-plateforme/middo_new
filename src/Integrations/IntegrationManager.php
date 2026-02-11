<?php

namespace App\Integrations;

use App\Entity\PartnerAction;
use App\Entity\PartnerConnector;
use Doctrine\ORM\EntityManagerInterface;

class IntegrationManager
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    public function executeAction(PartnerAction $action): void
    {
        $connector = $action->getPartnerConnector();
        $partner = $connector->getPartnerApp();

        // 1. Charger le driver correspondant au partenaire
        $driver = $this->loadDriver($partner->getName());

        if (!$driver) {
            $action->setStatus('failed');
            $action->setResult(['error' => 'Driver not found']);
            $this->em->flush();
            return;
        }

        // 2. Exécuter l’action
        $result = $driver->execute($connector, $action);

        // 3. Enregistrer le résultat
        $action->setResult($result);
        $action->setStatus('success');
        $action->setExecutedAt(new \DateTimeImmutable());

        $this->em->flush();
    }

    private function loadDriver(string $partnerName): ?object
    {
        $class = "App\\Integrations\\Drivers\\" . ucfirst($partnerName) . "Driver";

        if (!class_exists($class)) {
            return null;
        }

        return new $class();
    }
}

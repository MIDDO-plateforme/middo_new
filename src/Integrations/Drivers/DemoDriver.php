<?php

namespace App\Integrations\Drivers;

use App\Entity\PartnerConnector;
use App\Entity\PartnerAction;

class DemoDriver extends BaseDriver
{
    /**
     * Exécution d'une action de démonstration.
     * Retourne simplement un message de succès.
     */
    public function execute(PartnerConnector $connector, PartnerAction $action): array
    {
        return [
            'message' => 'DemoDriver executed successfully',
            'actionType' => $action->getActionType(),
            'parameters' => $action->getParameters(),
            'connectorUser' => $connector->getUser()->getEmail(),
            'partner' => $connector->getPartnerApp()->getName(),
            'timestamp' => (new \DateTimeImmutable())->format('Y-m-d H:i:s')
        ];
    }
}

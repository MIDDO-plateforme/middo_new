<?php

namespace App\Integrations\Drivers;

use App\Entity\PartnerConnector;
use App\Entity\PartnerAction;

abstract class BaseDriver
{
    /**
     * Vérifie si le driver est prêt (clé API, config, etc.)
     */
    public function isReady(PartnerConnector $connector): bool
    {
        return !empty($connector->getToken());
    }

    /**
     * Exécute une action partenaire.
     * Chaque driver concret doit implémenter cette méthode.
     */
    abstract public function execute(PartnerConnector $connector, PartnerAction $action): array;
}

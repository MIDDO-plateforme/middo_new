<?php

namespace App\Integrations;

use App\Integrations\Drivers\BaseDriver;

class IntegrationManager
{
    /**
     * @var BaseDriver[]
     */
    private array $drivers = [];

    public function registerDriver(BaseDriver $driver): void
    {
        $this->drivers[$driver->getName()] = $driver;
    }

    public function getDriver(string $name): ?BaseDriver
    {
        return $this->drivers[$name] ?? null;
    }

    public function getDrivers(): array
    {
        return $this->drivers;
    }
}

<?php

namespace App\IA\Cortex;

use App\IA\Cortex\Entity\CortexMemory;
use App\IA\Cortex\Repository\CortexMemoryRepository;

class CortexMemorySynchronizer
{
    public function __construct(
        private CortexMemoryRepository $repository,
        private CortexEngine $cortexEngine,
    ) {
    }

    public function loadAll(): void
    {
        $all = $this->repository->findAll();

        foreach ($all as $item) {
            /** @var CortexMemory $item */
            $this->cortexEngine->remember(
                $item->getMemoryKey(),
                $item->getMemoryValue()
            );
        }
    }

    public function persistAll(): void
    {
        $memory = $this->cortexEngine->getLongTermMemory();

        foreach ($memory as $key => $value) {
            $entity = $this->repository->findByKey($key) ?? (new CortexMemory())->setMemoryKey($key);
            $entity->setMemoryValue($value);
            $this->repository->save($entity);
        }
    }
}

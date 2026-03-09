<?php

namespace App\KernelPipelines;

class Pipeline
{
    public function __construct(private array $steps)
    {
    }

    public function run(array $input): array
    {
        $data = $input;

        foreach ($this->steps as $step) {
            $data = $step->process($data);
        }

        return $data;
    }
}

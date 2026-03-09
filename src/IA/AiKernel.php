<?php

namespace App\IA;

use App\IA\Cache\IaCache;
use App\IA\Memory\MemoryStore;
use App\IA\Monitoring\MonitoringManager;
use App\IA\Optimization\PromptCompressor;
use App\IA\Provider\IAProviderInterface;
use App\AI\Provider\ProviderRouter;

class AiKernel
{
    /** @var IAProviderInterface[] */
    private array $providers;

    public function __construct(
        iterable $providers,
        private MonitoringManager $monitoring,
        private IaCache $cache,
        private PromptCompressor $compressor,
        private ProviderRouter $selector,
        private MemoryStore $memory
    ) {
        $this->providers = is_array($providers) ? $providers : iterator_to_array($providers);
    }

    public function generate(string $prompt): string
    {
        $prompt = $this->compressor->compress($prompt);

        if ($cached = $this->cache->get($prompt)) {
            return $cached;
        }

        $orderedProviders = $this->selector->selectOrder($prompt, $this->providers);

        $lastError = null;

        foreach ($orderedProviders as $provider) {
            $start = microtime(true);
            $providerName = $provider->getName();

            try {
                $answer = $provider->generate($prompt);

                $durationMs = (microtime(true) - $start) * 1000;
                $this->monitoring->logRequest($providerName, $durationMs, true);

                $this->cache->set($prompt, $answer);
                $this->memory->rememberShort('last_prompt', $prompt);

                return $answer;
            } catch (\Throwable $e) {
                $durationMs = (microtime(true) - $start) * 1000;
                $this->monitoring->logRequest($providerName, $durationMs, false, $e->getMessage());
                $lastError = $e;
            }
        }

        throw new \RuntimeException('Tous les providers IA ont échoué', 0, $lastError);
    }
}

<?php

namespace App\Command;

use App\AI\DTO\AIRequest;
use App\AI\Provider\TokenCounterInterface;
use App\Infrastructure\IA\Provider\OpenAIProvider;
use App\Infrastructure\IA\Provider\AnthropicProvider;
use App\IA\AiEngine;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:ia:test',
    description: 'Test du moteur IA MIDDO OS + IA'
)]
final class AiEngineTestCommand extends Command
{
    public function __construct(
        private readonly string $openaiApiKey,
        private readonly string $openaiModel,
        private readonly string $anthropicApiKey,
        private readonly string $anthropicModel,
        private readonly TokenCounterInterface $counter,
        private readonly HttpClientInterface $client
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $engine = new AiEngine();

        $engine->registerProvider(new OpenAIProvider(
            $this->client,
            $this->openaiApiKey,
            $this->openaiModel,
            $this->counter
        ));

        $engine->registerProvider(new AnthropicProvider(
            $this->client,
            $this->anthropicApiKey,
            $this->anthropicModel,
            $this->counter
        ));

        $output->writeln("=== Providers ===");
        foreach ($engine->getProviders() as $name => $provider) {
            $output->writeln("$name => " . $provider->test());
        }

        $request = new AIRequest(
            $this->openaiModel,
            "Bonjour IA !"
        );

        $output->writeln("\n=== v1 generate ===");
        $output->writeln($engine->generate($this->openaiModel, $request)->getContent());

        $output->writeln("\n=== v2 fallback ===");
        $output->writeln($engine->generateWithFallback($this->openaiModel, $request)->getContent());

        $output->writeln("\n=== v3 best (balanced) ===");
        $output->writeln($engine->generateBest($this->openaiModel, $request)->getContent());

        $output->writeln("\n=== v4 racing ===");
        $output->writeln($engine->raceGenerate(
            [$this->openaiModel, $this->anthropicModel],
            $request
        )->getContent());

        return Command::SUCCESS;
    }
}

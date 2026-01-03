<?php

namespace App\Handler;

use App\Message\BlockchainTransactionMessage;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class BlockchainTransactionHandler
{
    private EntityManagerInterface $entityManager;
    private ?LoggerInterface $logger;

    public function __construct(
        EntityManagerInterface $entityManager,
        ?LoggerInterface $logger = null
    ) {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    public function __invoke(BlockchainTransactionMessage $message): void
    {
        $userId = $message->getUserId();
        $amount = $message->getAmount();
        $transactionType = $message->getTransactionType();
        $metadata = $message->getMetadata();

        $this->logger?->info('Processing blockchain transaction', [
            'user_id' => $userId,
            'amount' => $amount,
            'type' => $transactionType,
        ]);

        try {
            // Logique de transaction blockchain
            // À implémenter selon votre système blockchain
            
            $transactionHash = bin2hex(random_bytes(32));
            
            $this->logger?->info('Blockchain transaction completed', [
                'transaction_hash' => $transactionHash,
                'user_id' => $userId,
                'amount' => $amount,
            ]);
        } catch (\Exception $e) {
            $this->logger?->error('Blockchain transaction failed', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
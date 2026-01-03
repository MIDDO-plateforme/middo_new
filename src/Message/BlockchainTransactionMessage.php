<?php

namespace App\Message;

class BlockchainTransactionMessage
{
    private int $userId;
    private float $amount;
    private string $transactionType;
    private array $metadata;

    public function __construct(int $userId, float $amount, string $transactionType, array $metadata = [])
    {
        $this->userId = $userId;
        $this->amount = $amount;
        $this->transactionType = $transactionType;
        $this->metadata = $metadata;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getTransactionType(): string
    {
        return $this->transactionType;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }
}
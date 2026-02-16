<?php

namespace App\Service;

use App\Entity\Escrow;
use Doctrine\ORM\EntityManagerInterface;

class EscrowService
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    public function lockEscrow(int $amount, string $projectId): array
    {
        $escrow = new Escrow();
        $escrow->setAmount($amount);
        $escrow->setProjectId($projectId);
        $escrow->setStatus('locked');
        $escrow->setTxHash('0x' . bin2hex(random_bytes(32)));
        $escrow->setContractAddress('0x' . bin2hex(random_bytes(20)));
        $escrow->setLockedUntil(new \DateTime('+30 days'));

        $this->em->persist($escrow);
        $this->em->flush();

        return [
            'id' => $escrow->getId(),
            'amount' => $escrow->getAmount(),
            'status' => $escrow->getStatus(),
            'tx_hash' => $escrow->getTxHash(),
            'contract_address' => $escrow->getContractAddress(),
            'locked_until' => $escrow->getLockedUntil(),
        ];
    }

    public function releaseEscrow(int $escrowId): array
    {
        $escrow = $this->em->getRepository(Escrow::class)->find($escrowId);

        if (!$escrow) {
            return ['error' => 'Escrow not found'];
        }

        $escrow->setStatus('released');
        $escrow->setReleaseTxHash('0x' . bin2hex(random_bytes(32)));

        $this->em->flush();

        return [
            'id' => $escrow->getId(),
            'status' => $escrow->getStatus(),
            'release_tx_hash' => $escrow->getReleaseTxHash(),
        ];
    }
}

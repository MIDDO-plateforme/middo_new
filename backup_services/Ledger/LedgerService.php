<?php

namespace App\Service\Ledger;

use App\Entity\LedgerAccount;
use App\Entity\LedgerTransaction;
use App\Entity\LedgerEntry;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

/**
 * Service LedgerService - Gestion du système de comptabilité en partie double
 */
class LedgerService
{
    public function __construct(
        private EntityManagerInterface $em
    ) {
    }

    /**
     * Crée un nouveau compte ledger pour un utilisateur
     */
    public function createAccount(User $user, string $type, string $currency = 'EUR'): LedgerAccount
    {
        $account = new LedgerAccount();
        $account->setUserId($user->getId());
        $account->setAccountType($type);
        $account->setCurrency($currency);
        $account->setStatus('active');

        $this->em->persist($account);
        $this->em->flush();

        return $account;
    }

    /**
     * Enregistre une transaction avec plusieurs entrées
     */
    public function recordTransaction(array $entries, string $description, ?User $initiatedBy = null): LedgerTransaction
    {
        // Valide le double-entry
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($entries as $entry) {
            if ($entry['type'] === 'DEBIT') {
                $totalDebit += $entry['amount'];
            } else {
                $totalCredit += $entry['amount'];
            }
        }

        if ($totalDebit !== $totalCredit) {
            throw new \InvalidArgumentException(
                sprintf('Double-entry violation: DEBIT (%d) != CREDIT (%d)', $totalDebit, $totalCredit)
            );
        }

        // Crée la transaction
        $transaction = new LedgerTransaction();
        $transaction->setReferenceNumber(Uuid::v4()->toRfc4122());
        $transaction->setDescription($description);
        $transaction->setStatus('completed');
        
        if ($initiatedBy) {
            $transaction->setInitiatedBy($initiatedBy->getId());
        }

        $this->em->persist($transaction);
        $this->em->flush(); // Flush pour obtenir l'ID

        // Crée les entrées
        foreach ($entries as $entryData) {
            $entry = new LedgerEntry();
            $entry->setAccount($entryData['account']);
            $entry->setTransaction($transaction);
            $entry->setDirection($entryData['type']); // FIX: Utilise setDirection
            $entry->setAmountCents($entryData['amount']);
            $entry->setCurrency($entryData['account']->getCurrency());

            $this->em->persist($entry);
        }

        $this->em->flush();

        return $transaction;
    }

    /**
     * Transfert de fonds entre deux comptes
     */
    public function transferFunds(
        LedgerAccount $from,
        LedgerAccount $to,
        int $amountCents,
        string $description,
        ?User $initiatedBy = null
    ): LedgerTransaction {
        // Vérifie le solde
        $balance = $this->getBalance($from);
        if ($balance < $amountCents) {
            throw new \RuntimeException('Insufficient balance');
        }

        // Crée les entrées
        $entries = [
            [
                'account' => $from,
                'type' => 'DEBIT',
                'amount' => $amountCents,
            ],
            [
                'account' => $to,
                'type' => 'CREDIT',
                'amount' => $amountCents,
            ]
        ];

        return $this->recordTransaction($entries, $description, $initiatedBy);
    }

    /**
     * Calcule le solde d'un compte
     */
    public function getBalance(LedgerAccount $account): int
    {
        // FIX: Utilise e.direction au lieu de e.entryType
        $qb = $this->em->createQueryBuilder();
        
        $credits = $qb->select('SUM(e.amountCents)')
            ->from(LedgerEntry::class, 'e')
            ->where('e.account = :account')
            ->andWhere('e.direction = :type')
            ->setParameter('account', $account)
            ->setParameter('type', 'CREDIT')
            ->getQuery()
            ->getSingleScalarResult() ?? 0;

        $qb2 = $this->em->createQueryBuilder();
        
        $debits = $qb2->select('SUM(e.amountCents)')
            ->from(LedgerEntry::class, 'e')
            ->where('e.account = :account')
            ->andWhere('e.direction = :type')
            ->setParameter('account', $account)
            ->setParameter('type', 'DEBIT')
            ->getQuery()
            ->getSingleScalarResult() ?? 0;

        return (int) ($credits - $debits);
    }

    /**
     * Valide l'intégrité d'une transaction (double-entry)
     */
    public function validateDoubleEntry(LedgerTransaction $transaction): bool
    {
        $entries = $this->em->getRepository(LedgerEntry::class)
            ->findBy(['transaction' => $transaction]);

        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($entries as $entry) {
            if ($entry->getDirection() === 'DEBIT') {
                $totalDebit += $entry->getAmountCents();
            } else {
                $totalCredit += $entry->getAmountCents();
            }
        }

        return $totalDebit === $totalCredit;
    }

    /**
     * Récupère l'historique d'un compte
     */
    public function getAccountHistory(
        LedgerAccount $account,
        ?\DateTimeInterface $fromDate = null,
        ?\DateTimeInterface $toDate = null,
        ?int $limit = null
    ): array {
        $qb = $this->em->createQueryBuilder();
        
        $qb->select('e')
            ->from(LedgerEntry::class, 'e')
            ->where('e.account = :account')
            ->setParameter('account', $account)
            ->orderBy('e.createdAt', 'DESC');

        if ($fromDate) {
            $qb->andWhere('e.createdAt >= :fromDate')
               ->setParameter('fromDate', $fromDate);
        }

        if ($toDate) {
            $qb->andWhere('e.createdAt <= :toDate')
               ->setParameter('toDate', $toDate);
        }

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Clôture un compte (solde doit être 0)
     */
    public function closeAccount(LedgerAccount $account, User $closedBy): void
    {
        $balance = $this->getBalance($account);

        if ($balance !== 0) {
            throw new \RuntimeException(
                sprintf('Cannot close account with non-zero balance: %d cents', $balance)
            );
        }

        $account->setStatus('closed');
        $this->em->flush();
    }
}

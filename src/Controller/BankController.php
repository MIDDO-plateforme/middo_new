<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\LedgerAccount;
use App\Entity\LedgerTransaction;
use App\Service\Ledger\LedgerService;
use App\Form\TransferType;
use App\Form\DepositType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

#[Route('/bank')]
#[IsGranted('ROLE_USER')]
class BankController extends AbstractController
{
    public function __construct(
        private LedgerService $ledgerService,
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * Dashboard bancaire principal
     * Affiche le solde, l'historique des transactions, et les statistiques
     */
    #[Route('/dashboard', name: 'app_bank_dashboard', methods: ['GET'])]
    public function dashboard(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        // Récupère ou crée le compte principal de l'utilisateur
        $account = $this->getUserMainAccount($user);

        // Calcule le solde actuel
        $balance = $this->ledgerService->getBalance($account);

        // Récupère l'historique des 20 dernières transactions
        $history = $this->ledgerService->getAccountHistory($account, limit: 20);

        // Calcule les statistiques (7 derniers jours)
        $stats = $this->calculateStats($account);

        return $this->render('bank/dashboard.html.twig', [
            'account' => $account,
            'balance' => $balance,
            'balance_formatted' => number_format($balance / 100, 2, ',', ' '),
            'currency' => $account->getCurrency(),
            'history' => $history,
            'stats' => $stats,
        ]);
    }

    /**
     * Formulaire de transfert P2P entre utilisateurs
     */
    #[Route('/transfer', name: 'app_bank_transfer', methods: ['GET', 'POST'])]
    public function transfer(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $account = $this->getUserMainAccount($user);

        $form = $this->createForm(TransferType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            try {
                // Récupère le compte du destinataire
                $recipientEmail = $data['recipient_email'];
                $recipient = $this->entityManager->getRepository(User::class)
                    ->findOneBy(['email' => $recipientEmail]);

                if (!$recipient) {
                    $this->addFlash('error', 'Destinataire introuvable. Vérifiez l\'adresse email.');
                    return $this->redirectToRoute('app_bank_transfer');
                }

                if ($recipient->getId() === $user->getId()) {
                    $this->addFlash('error', 'Vous ne pouvez pas effectuer un transfert vers vous-même.');
                    return $this->redirectToRoute('app_bank_transfer');
                }

                $recipientAccount = $this->getUserMainAccount($recipient);

                // Convertit le montant en centimes
                $amountCents = (int) ($data['amount'] * 100);

                // Vérifie le solde suffisant
                $currentBalance = $this->ledgerService->getBalance($account);
                if ($currentBalance < $amountCents) {
                    $this->addFlash('error', 'Solde insuffisant pour effectuer ce transfert.');
                    return $this->redirectToRoute('app_bank_transfer');
                }

                // Effectue le transfert via LedgerService
                $transaction = $this->ledgerService->transferFunds(
                    from: $account,
                    to: $recipientAccount,
                    amountCents: $amountCents,
                    description: $data['description'] ?? 'Transfert P2P',
                    initiatedBy: $user
                );

                $this->addFlash('success', sprintf(
                    'Transfert de %s € effectué avec succès vers %s',
                    number_format($data['amount'], 2, ',', ' '),
                    $recipient->getEmail()
                ));

                return $this->redirectToRoute('app_bank_dashboard');

            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors du transfert : ' . $e->getMessage());
                return $this->redirectToRoute('app_bank_transfer');
            }
        }

        return $this->render('bank/transfer.html.twig', [
            'form' => $form->createView(),
            'account' => $account,
            'balance' => $this->ledgerService->getBalance($account),
        ]);
    }

    /**
     * Page de dépôt via Stripe Checkout
     */
    #[Route('/deposit', name: 'app_bank_deposit', methods: ['GET', 'POST'])]
    public function deposit(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $account = $this->getUserMainAccount($user);

        $form = $this->createForm(DepositType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $amountCents = (int) ($data['amount'] * 100);

            try {
                // Configure Stripe avec la clé secrète
                Stripe::setApiKey($this->getParameter('stripe_secret_key'));

                // Crée une session Stripe Checkout
                $checkoutSession = StripeSession::create([
                    'payment_method_types' => ['card'],
                    'line_items' => [[
                        'price_data' => [
                            'currency' => strtolower($account->getCurrency()),
                            'product_data' => [
                                'name' => 'Dépôt MIDDO',
                                'description' => 'Recharge de votre compte MIDDO',
                            ],
                            'unit_amount' => $amountCents,
                        ],
                        'quantity' => 1,
                    ]],
                    'mode' => 'payment',
                    'success_url' => $this->generateUrl('app_bank_deposit_success', [
                        'session_id' => '{CHECKOUT_SESSION_ID}'
                    ], 0),
                    'cancel_url' => $this->generateUrl('app_bank_deposit_cancel', [], 0),
                    'client_reference_id' => $user->getId(),
                    'metadata' => [
                        'user_id' => $user->getId(),
                        'account_id' => $account->getId(),
                        'amount_cents' => $amountCents,
                    ],
                ]);

                // Redirige vers Stripe Checkout
                return $this->redirect($checkoutSession->url);

            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la création du paiement : ' . $e->getMessage());
                return $this->redirectToRoute('app_bank_deposit');
            }
        }

        return $this->render('bank/deposit.html.twig', [
            'form' => $form->createView(),
            'account' => $account,
        ]);
    }

    /**
     * Page de succès après dépôt Stripe
     */
    #[Route('/deposit/success', name: 'app_bank_deposit_success', methods: ['GET'])]
    public function depositSuccess(Request $request): Response
    {
        $sessionId = $request->query->get('session_id');

        if (!$sessionId) {
            return $this->redirectToRoute('app_bank_dashboard');
        }

        // Note : Le webhook Stripe s'occupe de créditer le compte
        // Cette page affiche uniquement une confirmation visuelle

        $this->addFlash('success', 'Paiement effectué avec succès ! Votre compte sera crédité dans quelques instants.');

        return $this->redirectToRoute('app_bank_dashboard');
    }

    /**
     * Page d'annulation de dépôt Stripe
     */
    #[Route('/deposit/cancel', name: 'app_bank_deposit_cancel', methods: ['GET'])]
    public function depositCancel(): Response
    {
        $this->addFlash('warning', 'Paiement annulé. Aucun montant n\'a été débité.');
        return $this->redirectToRoute('app_bank_deposit');
    }

    /**
     * Webhook Stripe pour traiter les paiements
     * IMPORTANT : À configurer dans le Dashboard Stripe
     */
    #[Route('/webhook/stripe', name: 'app_bank_webhook_stripe', methods: ['POST'])]
    public function stripeWebhook(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $sigHeader = $request->headers->get('Stripe-Signature');
        $endpointSecret = $this->getParameter('stripe_webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sigHeader,
                $endpointSecret
            );
        } catch (\UnexpectedValueException $e) {
            return new JsonResponse(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return new JsonResponse(['error' => 'Invalid signature'], 400);
        }

        // Traite l'événement checkout.session.completed
        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            $userId = $session->metadata->user_id ?? null;
            $accountId = $session->metadata->account_id ?? null;
            $amountCents = $session->metadata->amount_cents ?? null;

            if ($userId && $accountId && $amountCents) {
                // Récupère l'utilisateur et son compte
                $user = $this->entityManager->getRepository(User::class)->find($userId);
                $account = $this->entityManager->getRepository(LedgerAccount::class)->find($accountId);

                if ($user && $account) {
                    // Crée un compte "système" pour les dépôts Stripe
                    $stripeAccount = $this->getSystemAccount('stripe_deposits');

                    // Enregistre le dépôt comme transfert du système vers l'utilisateur
                    $this->ledgerService->transferFunds(
                        from: $stripeAccount,
                        to: $account,
                        amountCents: (int) $amountCents,
                        description: 'Dépôt Stripe - Session ' . $session->id,
                        initiatedBy: $user
                    );
                }
            }
        }

        return new JsonResponse(['status' => 'success']);
    }

    /**
     * Demande de retrait vers compte bancaire
     */
    #[Route('/withdraw', name: 'app_bank_withdraw', methods: ['GET', 'POST'])]
    public function withdraw(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $account = $this->getUserMainAccount($user);
        $balance = $this->ledgerService->getBalance($account);

        if ($request->isMethod('POST')) {
            $amount = (float) $request->request->get('amount');
            $amountCents = (int) ($amount * 100);

            // Validations
            if ($amountCents <= 0) {
                $this->addFlash('error', 'Le montant doit être supérieur à 0.');
                return $this->redirectToRoute('app_bank_withdraw');
            }

            if ($amountCents > $balance) {
                $this->addFlash('error', 'Solde insuffisant pour ce retrait.');
                return $this->redirectToRoute('app_bank_withdraw');
            }

            // Minimum de retrait : 10 €
            if ($amountCents < 1000) {
                $this->addFlash('error', 'Le montant minimum de retrait est de 10 €.');
                return $this->redirectToRoute('app_bank_withdraw');
            }

            try {
                // Crée un compte système pour les retraits
                $withdrawalAccount = $this->getSystemAccount('withdrawals_pending');

                // Transfère vers le compte de retraits en attente
                $this->ledgerService->transferFunds(
                    from: $account,
                    to: $withdrawalAccount,
                    amountCents: $amountCents,
                    description: 'Demande de retrait - En attente de traitement',
                    initiatedBy: $user
                );

                $this->addFlash('success', sprintf(
                    'Demande de retrait de %s € enregistrée. Traitement sous 2-3 jours ouvrés.',
                    number_format($amount, 2, ',', ' ')
                ));

                // TODO : Envoyer notification admin + email utilisateur

                return $this->redirectToRoute('app_bank_dashboard');

            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la demande de retrait : ' . $e->getMessage());
                return $this->redirectToRoute('app_bank_withdraw');
            }
        }

        return $this->render('bank/withdraw.html.twig', [
            'account' => $account,
            'balance' => $balance,
            'balance_formatted' => number_format($balance / 100, 2, ',', ' '),
        ]);
    }

    /**
     * API : Recherche d'utilisateurs pour transfert
     */
    #[Route('/api/search-users', name: 'app_bank_api_search_users', methods: ['GET'])]
    public function searchUsers(Request $request): JsonResponse
    {
        $query = $request->query->get('q', '');

        if (strlen($query) < 2) {
            return new JsonResponse([]);
        }

        $users = $this->entityManager->getRepository(User::class)
            ->createQueryBuilder('u')
            ->where('u.email LIKE :query OR u.firstName LIKE :query OR u.lastName LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();

        $results = [];
        foreach ($users as $user) {
            // N'affiche pas l'utilisateur actuel
            if ($user->getId() === $this->getUser()->getId()) {
                continue;
            }

            $results[] = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'name' => $user->getFirstName() . ' ' . $user->getLastName(),
            ];
        }

        return new JsonResponse($results);
    }

    // ========== MÉTHODES PRIVÉES ==========

    /**
     * Récupère ou crée le compte principal d'un utilisateur
     */
    private function getUserMainAccount(User $user): LedgerAccount
    {
        // FIX: Utilise userId au lieu de user
        $account = $this->entityManager->getRepository(LedgerAccount::class)
            ->findOneBy([
                'userId' => $user->getId(),
                'accountType' => 'ASSET',
                'status' => 'active'
            ]);

        if (!$account) {
            $account = $this->ledgerService->createAccount($user, 'ASSET', 'EUR');
        }

        return $account;
    }

    /**
     * Récupère ou crée un compte système (pour Stripe, retraits, etc.)
     */
    private function getSystemAccount(string $type): LedgerAccount
    {
        $accountNumber = 'SYSTEM_' . strtoupper($type);

        $account = $this->entityManager->getRepository(LedgerAccount::class)
            ->findOneBy(['accountNumber' => $accountNumber]);

        if (!$account) {
            $account = new LedgerAccount();
            $account->setAccountNumber($accountNumber);
            $account->setAccountType('LIABILITY');
            $account->setCurrency('EUR');
            $account->setStatus('active');

            $this->entityManager->persist($account);
            $this->entityManager->flush();
        }

        return $account;
    }

    /**
     * Calcule les statistiques des 7 derniers jours
     */
    private function calculateStats(LedgerAccount $account): array
    {
        $sevenDaysAgo = new \DateTime('-7 days');
        $history = $this->ledgerService->getAccountHistory($account, $sevenDaysAgo);

        $totalIn = 0;
        $totalOut = 0;
        $transactionCount = 0;

        foreach ($history as $entry) {
            $transactionCount++;
            if ($entry->getEntryType() === 'CREDIT') {
                $totalIn += $entry->getAmountCents();
            } else {
                $totalOut += $entry->getAmountCents();
            }
        }

        return [
            'total_in' => $totalIn,
            'total_out' => $totalOut,
            'transaction_count' => $transactionCount,
            'total_in_formatted' => number_format($totalIn / 100, 2, ',', ' '),
            'total_out_formatted' => number_format($totalOut / 100, 2, ',', ' '),
        ];
    }
}

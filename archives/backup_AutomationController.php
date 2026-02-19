<?php

namespace App\Controller;

use App\Message\NotificationMassMessage;
use App\Message\BlockchainTransactionMessage;
use App\Message\DatabaseBackupMessage;
use App\Message\ReportGeneratorMessage;
use App\Service\ReportService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/automation', name: 'api_automation_')]
class AutomationController extends AbstractController
{
    private MessageBusInterface $messageBus;
    private ReportService $reportService;

    public function __construct(
        MessageBusInterface $messageBus,
        ReportService $reportService
    ) {
        $this->messageBus = $messageBus;
        $this->reportService = $reportService;
    }

    #[Route('/test', name: 'test', methods: ['GET'])]
    public function test(): JsonResponse
    {
        return $this->json([
            'success' => true,
            'message' => 'API Automation MIDDO opérationnelle',
            'version' => 'SESSION_53',
            'features' => [
                'notification_mass',
                'blockchain_transaction',
                'database_backup',
                'report_generation',
            ],
            'timestamp' => (new \DateTime())->format('Y-m-d H:i:s'),
        ]);
    }

    #[Route('/stats', name: 'stats', methods: ['GET'])]
    public function getStats(): JsonResponse
    {
        try {
            $stats = $this->reportService->getGlobalStats();
            
            return $this->json([
                'success' => true,
                'data' => $stats,
                'timestamp' => (new \DateTime())->format('Y-m-d H:i:s'),
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/notification/send', name: 'notification_send', methods: ['POST'])]
    public function sendNotificationMass(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            $userIds = $data['user_ids'] ?? [];
            $message = $data['message'] ?? '';
            $type = $data['type'] ?? 'info';

            if (empty($userIds) || empty($message)) {
                return $this->json([
                    'success' => false,
                    'error' => 'user_ids et message sont requis',
                ], 400);
            }

            $notificationMessage = new NotificationMassMessage($userIds, $message, $type);
            $this->messageBus->dispatch($notificationMessage);

            return $this->json([
                'success' => true,
                'message' => 'Notification masse envoyée en file d\'attente',
                'dispatched' => true,
                'user_count' => count($userIds),
                'timestamp' => (new \DateTime())->format('Y-m-d H:i:s'),
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/blockchain/transaction', name: 'blockchain_transaction', methods: ['POST'])]
    public function processBlockchainTransaction(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            $userId = $data['user_id'] ?? null;
            $amount = $data['amount'] ?? null;
            $transactionType = $data['transaction_type'] ?? 'payment';
            $metadata = $data['metadata'] ?? [];

            if (!$userId || !$amount) {
                return $this->json([
                    'success' => false,
                    'error' => 'user_id et amount sont requis',
                ], 400);
            }

            $blockchainMessage = new BlockchainTransactionMessage(
                $userId,
                (float)$amount,
                $transactionType,
                $metadata
            );
            $this->messageBus->dispatch($blockchainMessage);

            return $this->json([
                'success' => true,
                'message' => 'Transaction blockchain en cours de traitement',
                'dispatched' => true,
                'user_id' => $userId,
                'amount' => $amount,
                'timestamp' => (new \DateTime())->format('Y-m-d H:i:s'),
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/backup/database', name: 'backup_database', methods: ['POST'])]
    public function backupDatabase(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true) ?? [];
            
            $backupType = $data['backup_type'] ?? 'full';
            $options = $data['options'] ?? [];

            $backupMessage = new DatabaseBackupMessage($backupType, $options);
            $this->messageBus->dispatch($backupMessage);

            return $this->json([
                'success' => true,
                'message' => 'Backup base de données en cours',
                'dispatched' => true,
                'backup_type' => $backupType,
                'timestamp' => (new \DateTime())->format('Y-m-d H:i:s'),
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/report/generate', name: 'report_generate', methods: ['POST'])]
    public function generateReport(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            $reportType = $data['type'] ?? 'summary';
            $format = $data['format'] ?? 'pdf';
            $filters = $data['filters'] ?? [];

            $reportMessage = new ReportGeneratorMessage($reportType, $format, $filters);
            $this->messageBus->dispatch($reportMessage);

            return $this->json([
                'success' => true,
                'message' => 'Génération du rapport en cours',
                'dispatched' => true,
                'report_type' => $reportType,
                'format' => $format,
                'timestamp' => (new \DateTime())->format('Y-m-d H:i:s'),
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
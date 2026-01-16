<?php

namespace App\Controller;

use App\Service\ReportService;
use App\Service\PDFGenerator;
use App\Service\CSVExporter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/reports', name: 'api_reports_')]
class ReportController extends AbstractController
{
    private ReportService $reportService;
    private PDFGenerator $pdfGenerator;
    private CSVExporter $csvExporter;

    public function __construct(
        ReportService $reportService,
        PDFGenerator $pdfGenerator,
        CSVExporter $csvExporter
    ) {
        $this->reportService = $reportService;
        $this->pdfGenerator = $pdfGenerator;
        $this->csvExporter = $csvExporter;
    }

    /**
     * Test de l'API Reports
     */
    #[Route('/test', name: 'test', methods: ['GET'])]
    public function test(): JsonResponse
    {
        return new JsonResponse([
            'success' => true,
            'message' => 'API Reports MIDDO operationnelle',
            'session' => 'SESSION 55 - Business Intelligence',
            'version' => '1.0.0',
            'routes' => [
                '/api/reports/test',
                '/api/reports/stats',
                '/api/reports/project/{id}/pdf',
                '/api/reports/tasks/export',
                '/api/reports/notifications/export',
                '/api/reports/users/activity',
                '/api/reports/custom',
                '/api/reports/insights',
            ],
        ]);
    }

    /**
     * Obtenir les statistiques globales
     */
    #[Route('/stats', name: 'stats', methods: ['GET'])]
    public function getStats(): JsonResponse
    {
        try {
            $stats = $this->reportService->getGlobalStats();

            return new JsonResponse([
                'success' => true,
                'data' => $stats,
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Générer un rapport projet en PDF
     */
    #[Route('/project/{id}/pdf', name: 'project_pdf', methods: ['GET'])]
    public function generateProjectPDF(int $id): Response
    {
        try {
            $projectData = $this->reportService->getProjectData($id);
            $pdfPath = $this->pdfGenerator->generateProjectReport($projectData);

            $fullPath = $this->getParameter('kernel.project_dir') . '/public' . $pdfPath;

            if (!file_exists($fullPath)) {
                return new JsonResponse([
                    'success' => false,
                    'error' => 'PDF generation failed',
                ], 500);
            }

            return new BinaryFileResponse($fullPath);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Générer un rapport stats en PDF
     */
    #[Route('/stats/pdf', name: 'stats_pdf', methods: ['GET'])]
    public function generateStatsPDF(): Response
    {
        try {
            $stats = $this->reportService->getGlobalStats();
            $pdfPath = $this->pdfGenerator->generateStatsReport($stats);

            $fullPath = $this->getParameter('kernel.project_dir') . '/public' . $pdfPath;

            if (!file_exists($fullPath)) {
                return new JsonResponse([
                    'success' => false,
                    'error' => 'PDF generation failed',
                ], 500);
            }

            return new BinaryFileResponse($fullPath);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Exporter les tâches en CSV
     */
    #[Route('/tasks/export', name: 'tasks_export', methods: ['GET'])]
    public function exportTasks(Request $request): Response
    {
        try {
            $filters = $request->query->all();
            $tasks = $this->reportService->getTasksForExport($filters);
            $csvPath = $this->csvExporter->exportTasks($tasks);

            $fullPath = $this->getParameter('kernel.project_dir') . '/public' . $csvPath;

            if (!file_exists($fullPath)) {
                return new JsonResponse([
                    'success' => false,
                    'error' => 'CSV export failed',
                ], 500);
            }

            return new BinaryFileResponse($fullPath);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Exporter les notifications en CSV
     */
    #[Route('/notifications/export', name: 'notifications_export', methods: ['GET'])]
    public function exportNotifications(Request $request): Response
    {
        try {
            $filters = $request->query->all();
            $notifications = $this->reportService->getNotificationsForExport($filters);
            $csvPath = $this->csvExporter->exportNotifications($notifications);

            $fullPath = $this->getParameter('kernel.project_dir') . '/public' . $csvPath;

            if (!file_exists($fullPath)) {
                return new JsonResponse([
                    'success' => false,
                    'error' => 'CSV export failed',
                ], 500);
            }

            return new BinaryFileResponse($fullPath);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Exporter les stats en CSV
     */
    #[Route('/stats/export', name: 'stats_export', methods: ['GET'])]
    public function exportStats(): Response
    {
        try {
            $stats = $this->reportService->getGlobalStats();
            $csvPath = $this->csvExporter->exportStats($stats);

            $fullPath = $this->getParameter('kernel.project_dir') . '/public' . $csvPath;

            if (!file_exists($fullPath)) {
                return new JsonResponse([
                    'success' => false,
                    'error' => 'CSV export failed',
                ], 500);
            }

            return new BinaryFileResponse($fullPath);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtenir l'activité des utilisateurs
     */
    #[Route('/users/activity', name: 'users_activity', methods: ['GET'])]
    public function getUsersActivity(): JsonResponse
    {
        try {
            $activity = $this->reportService->getUsersActivity();

            return new JsonResponse([
                'success' => true,
                'data' => $activity,
                'timestamp' => (new \DateTime())->format('Y-m-d H:i:s'),
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Exporter l'activité des utilisateurs en CSV
     */
    #[Route('/users/activity/export', name: 'users_activity_export', methods: ['GET'])]
    public function exportUsersActivity(): Response
    {
        try {
            $activity = $this->reportService->getUsersActivity();
            $csvPath = $this->csvExporter->exportUsersActivity($activity);

            $fullPath = $this->getParameter('kernel.project_dir') . '/public' . $csvPath;

            if (!file_exists($fullPath)) {
                return new JsonResponse([
                    'success' => false,
                    'error' => 'CSV export failed',
                ], 500);
            }

            return new BinaryFileResponse($fullPath);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Générer un rapport personnalisé
     */
    #[Route('/custom', name: 'custom', methods: ['POST'])]
    public function generateCustomReport(Request $request): JsonResponse
    {
        try {
            $params = json_decode($request->getContent(), true);
            $report = $this->reportService->generateCustomReport($params);

            return new JsonResponse([
                'success' => true,
                'data' => $report,
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtenir les insights IA
     */
    #[Route('/insights', name: 'insights', methods: ['GET'])]
    public function getInsights(): JsonResponse
    {
        try {
            $insights = $this->reportService->generateInsights();

            return new JsonResponse([
                'success' => true,
                'data' => $insights,
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

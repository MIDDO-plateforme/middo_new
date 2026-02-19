<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class EmailController extends AbstractController
{
        #[Route('/api/email/send-dashboard', name: 'email_send_dashboard', methods: ['GET'])]
    public function sendDailyDashboard(MailerInterface $mailer): Response
    {
        try {
            // Données statiques (pas besoin d'appeler l'API)
            $emailData = [
                'date' => date('d/m/Y à H:i'),
                'taux_completion' => 40,
                'score_productivite' => 20,
                'taches_total' => 5,
                'utilisateurs_total' => 5,
                'chart_completion_url' => 'http://localhost:8000/images/charts/chart_completion.png',
                'chart_productivity_url' => 'http://localhost:8000/images/charts/chart_productivity.png',
                'chart_tasks_url' => 'http://localhost:8000/images/charts/chart_tasks.png',
                'chart_notifications_url' => 'http://localhost:8000/images/charts/chart_notifications.png',
            ];

            // Générer le HTML
            $htmlContent = $this->renderView('email/daily_dashboard.html.twig', $emailData);

            // Créer et envoyer l'email
            $email = (new Email())
                ->from('noreply@middo.com')
                ->to('mbane.baudouin@gmail.com') // ⚠️ TON EMAIL ICI
                ->subject('📊 MIDDO Dashboard Quotidien - ' . date('d/m/Y'))
                ->html($htmlContent);

            $mailer->send($email);

            // Retourner succès
            return $this->json([
                'success' => true,
                'message' => 'Email envoyé avec succès à mbane.baudouin@gmail.com',
                'timestamp' => date('Y-m-d H:i:s')
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    #[Route('/api/email/preview-dashboard', name: 'email_preview_dashboard', methods: ['GET'])]
    public function previewDailyDashboard(HttpClientInterface $httpClient): Response
    {
        try {
            // Récupérer les stats depuis l'API
            $response = $httpClient->request('GET', 'http://localhost:8000/api/automation/stats');
            $stats = $response->toArray();

            // Données pour le template
            $emailData = [
                'date' => date('d/m/Y à H:i'),
                'taux_completion' => $stats['kpi_scores']['completion_rate'] ?? 0,
                'score_productivite' => $stats['kpi_scores']['productivity_score'] ?? 0,
                'taches_total' => $stats['tasks']['total'] ?? 0,
                'utilisateurs_total' => $stats['users']['total_users'] ?? 0,
                'chart_completion_url' => '/images/charts/chart_completion.png',
                'chart_productivity_url' => '/images/charts/chart_productivity.png',
                'chart_tasks_url' => '/images/charts/chart_tasks.png',
                'chart_notifications_url' => '/images/charts/chart_notifications.png',
            ];

            // Afficher le template directement (pour preview)
            return $this->render('email/daily_dashboard.html.twig', $emailData);

        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

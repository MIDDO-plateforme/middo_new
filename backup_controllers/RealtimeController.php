<?php

namespace App\Controller;

use App\Repository\NotificationRepository;
use App\Service\NotificationPusher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/realtime', name: 'api_realtime_')]
class RealtimeController extends AbstractController
{
    private NotificationRepository $notificationRepository;
    private NotificationPusher $notificationPusher;

    public function __construct(
        NotificationRepository $notificationRepository,
        NotificationPusher $notificationPusher
    ) {
        $this->notificationRepository = $notificationRepository;
        $this->notificationPusher = $notificationPusher;
    }

    /**
     * GET /api/realtime/test
     * Test que l'API temps réel est active
     */
    #[Route('/test', name: 'test', methods: ['GET'])]
    public function test(): JsonResponse
    {
        return $this->json([
            'success' => true,
            'message' => 'API Realtime MIDDO operationnelle',
            'session' => 'SESSION 39 - Notifications Temps Reel',
            'version' => '1.0.0',
            'features' => [
                'Server-Sent Events (SSE)',
                'Notifications push instantanees',
                'Polling fallback'
            ],
            'timestamp' => (new \DateTime())->format('Y-m-d H:i:s')
        ]);
    }

    /**
     * GET /api/realtime/notifications/stream
     * Stream SSE pour les notifications en temps réel
     * 
     * Usage frontend:
     * const eventSource = new EventSource('/api/realtime/notifications/stream?userId=1');
     * eventSource.onmessage = (event) => { console.log(JSON.parse(event.data)); };
     */
    #[Route('/notifications/stream', name: 'notifications_stream', methods: ['GET'])]
    public function notificationsStream(Request $request): StreamedResponse
    {
        $userId = $request->query->get('userId', 1); // En production, utiliser l'auth réelle

        $response = new StreamedResponse();
        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('X-Accel-Buffering', 'no');

        $response->setCallback(function() use ($userId) {
            $lastId = 0;
            
            // Envoyer un message de connexion
            echo "data: " . json_encode([
                'type' => 'connected',
                'message' => 'Stream SSE connecte',
                'timestamp' => (new \DateTime())->format('Y-m-d H:i:s')
            ]) . "\n\n";
            ob_flush();
            flush();

            // Boucle de polling (30 secondes max pour éviter timeout)
            $startTime = time();
            while ((time() - $startTime) < 30) {
                $notifications = $this->notificationRepository->createQueryBuilder('n')
                    ->where('n.user = :userId')
                    ->andWhere('n.id > :lastId')
                    ->setParameter('userId', $userId)
                    ->setParameter('lastId', $lastId)
                    ->orderBy('n.id', 'ASC')
                    ->getQuery()
                    ->getResult();

                foreach ($notifications as $notification) {
                    $data = [
                        'id' => $notification->getId(),
                        'title' => $notification->getTitle(),
                        'message' => $notification->getMessage(),
                        'type' => $notification->getType(),
                        'created_at' => $notification->getCreatedAt()->format('Y-m-d H:i:s'),
                        'data' => $notification->getData()
                    ];

                    echo "data: " . json_encode($data) . "\n\n";
                    ob_flush();
                    flush();

                    $lastId = $notification->getId();
                }

                // Attendre 2 secondes avant le prochain check
                sleep(2);

                // Vérifier si la connexion est toujours active
                if (connection_aborted()) {
                    break;
                }
            }

            // Message de déconnexion
            echo "data: " . json_encode([
                'type' => 'disconnected',
                'message' => 'Stream SSE ferme (timeout 30s)',
                'timestamp' => (new \DateTime())->format('Y-m-d H:i:s')
            ]) . "\n\n";
            ob_flush();
            flush();
        });

        return $response;
    }

    /**
     * GET /api/realtime/notifications/poll
     * Polling simple (fallback si SSE ne marche pas)
     */
    #[Route('/notifications/poll', name: 'notifications_poll', methods: ['GET'])]
    public function notificationsPoll(Request $request): JsonResponse
    {
        $userId = $request->query->get('userId', 1);
        $lastId = $request->query->get('lastId', 0);

        $notifications = $this->notificationRepository->createQueryBuilder('n')
            ->where('n.user = :userId')
            ->andWhere('n.id > :lastId')
            ->setParameter('userId', $userId)
            ->setParameter('lastId', $lastId)
            ->orderBy('n.id', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();

        $data = [];
        foreach ($notifications as $notification) {
            $data[] = [
                'id' => $notification->getId(),
                'title' => $notification->getTitle(),
                'message' => $notification->getMessage(),
                'type' => $notification->getType(),
                'is_read' => $notification->isRead(),
                'created_at' => $notification->getCreatedAt()->format('Y-m-d H:i:s')
            ];
        }

        return $this->json([
            'success' => true,
            'notifications' => $data,
            'count' => count($data),
            'timestamp' => (new \DateTime())->format('Y-m-d H:i:s')
        ]);
    }

    /**
     * POST /api/realtime/notifications/push-test
     * Créer une notification de test (pour démonstration)
     */
    #[Route('/notifications/push-test', name: 'notifications_push_test', methods: ['POST'])]
    public function pushTestNotification(Request $request): JsonResponse
    {
        $userId = $request->query->get('userId', 1);
        $user = $this->notificationRepository->createQueryBuilder('n')
            ->select('u')
            ->from('App\Entity\User', 'u')
            ->where('u.id = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$user) {
            return $this->json(['success' => false, 'error' => 'Utilisateur non trouve'], 404);
        }

        $notification = $this->notificationPusher->pushTestNotification($user);

        return $this->json([
            'success' => true,
            'message' => 'Notification test creee',
            'notification' => [
                'id' => $notification->getId(),
                'title' => $notification->getTitle(),
                'message' => $notification->getMessage(),
                'type' => $notification->getType()
            ],
            'timestamp' => (new \DateTime())->format('Y-m-d H:i:s')
        ]);
    }
}

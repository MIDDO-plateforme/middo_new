<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

class TestController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/test-debug', name: 'app_test_debug', methods: ['GET'])]
    public function debug(): Response
    {
        if (($_ENV['APP_ENV'] ?? 'dev') === 'prod') {
            throw $this->createAccessDeniedException('Désactivé en prod');
        }

        $env = $_ENV['APP_ENV'] ?? 'dev';
        $php = PHP_VERSION;
        $sf = \Symfony\Component\HttpKernel\Kernel::VERSION;
        $dbConnected = $this->testDatabaseConnection() ? '✅ CONNECTÉE' : '❌ DÉCONNECTÉE';

        return new Response("<!DOCTYPE html><html><head><meta charset='UTF-8'><title>MIDDO Debug</title><style>body{font-family:system-ui;background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;display:flex;justify-content:center;align-items:center;min-height:100vh;margin:0}.container{text-align:center;background:rgba(255,255,255,.1);padding:50px;border-radius:20px;backdrop-filter:blur(10px);max-width:600px}h1{font-size:3.5em;margin:0 0 20px;font-weight:800}.badge{display:inline-block;padding:10px 25px;background:#10b981;border-radius:25px;font-weight:700;margin:20px 0}.info-grid{display:grid;grid-template-columns:1fr 1fr;gap:15px;margin-top:30px;text-align:left}.info-item{background:rgba(255,255,255,.15);padding:15px;border-radius:10px}.info-label{font-size:.9em;opacity:.8;margin-bottom:5px}.info-value{font-size:1.3em;font-weight:700}.db-status{grid-column:1/-1;text-align:center;font-size:1.5em;padding:20px}</style></head><body><div class='container'><h1>✅ MIDDO TEST</h1><div class='badge'>Symfony Opérationnel</div><div class='info-grid'><div class='info-item'><div class='info-label'>Environnement</div><div class='info-value'>".strtoupper($env)."</div></div><div class='info-item'><div class='info-label'>Timestamp</div><div class='info-value'>".date('H:i:s')."</div></div><div class='info-item'><div class='info-label'>PHP Version</div><div class='info-value'>$php</div></div><div class='info-item'><div class='info-label'>Symfony Version</div><div class='info-value'>$sf</div></div><div class='info-item db-status'><div class='info-label'>Base de données</div><div class='info-value'>$dbConnected</div></div></div><p style='margin-top:30px;font-size:.9em;opacity:.7'>🚀 MIDDO Platform - Système de diagnostic complet</p></div></body></html>");
    }

    #[Route('/api/test', name: 'api_test', methods: ['GET'])]
    public function testApi(): JsonResponse
    {
        return $this->json([
            'status' => 'ok',
            'timestamp' => (new \DateTime())->format('Y-m-d H:i:s'),
            'environment' => $_ENV['APP_ENV'] ?? 'dev',
            'system' => [
                'php_version' => PHP_VERSION,
                'symfony_version' => \Symfony\Component\HttpKernel\Kernel::VERSION,
                'database_connected' => $this->testDatabaseConnection(),
            ]
        ]);
    }

    /**
     * Teste la connexion à la base de données en exécutant une requête simple
     * 
     * @return bool true si la connexion fonctionne, false sinon
     */
    private function testDatabaseConnection(): bool
    {
        try {
            $connection = $this->entityManager->getConnection();
            $connection->executeQuery('SELECT 1')->fetchOne();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}

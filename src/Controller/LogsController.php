<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

class LogsController
{
    #[Route('/api/logs/stream', name: 'api_logs_stream')]
    public function stream(): StreamedResponse
    {
        $response = new StreamedResponse(function () {
            while (true) {
                $log = [
                    'time' => (new \DateTime())->format('H:i:s'),
                    'message' => 'Cortex actif — heartbeat OK',
                ];

                echo 'data: ' . json_encode($log) . "\n\n";
                @ob_flush();
                @flush();
                usleep(500000);
            }
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');

        return $response;
    }
}

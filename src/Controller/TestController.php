<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TestController extends AbstractController
{
    #[Route('/test-debug', name: 'app_test_debug', methods: ['GET'])]
    public function debug(): Response
    {
        return new Response('
            <!DOCTYPE html>
            <html>
            <head>
                <title>TEST DEBUG</title>
                <style>
                    body { 
                        font-family: Arial; 
                        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                        color: white;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        height: 100vh;
                        margin: 0;
                    }
                    .container {
                        text-align: center;
                        background: rgba(255,255,255,0.1);
                        padding: 40px;
                        border-radius: 20px;
                        backdrop-filter: blur(10px);
                    }
                    h1 { font-size: 3em; margin: 0; }
                    p { font-size: 1.5em; }
                </style>
            </head>
            <body>
                <div class="container">
                    <h1> TEST OK !</h1>
                    <p>Symfony fonctionne correctement</p>
                    <p>Date : ' . date('Y-m-d H:i:s') . '</p>
                </div>
            </body>
            </html>
        ');
    }
}

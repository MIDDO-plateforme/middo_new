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
                <title>TEST DEBUG MIDDO</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
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
                        padding: 50px;
                        border-radius: 20px;
                        backdrop-filter: blur(10px);
                        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
                    }
                    h1 {
                        font-size: 3.5em;
                        margin: 0 0 20px 0;
                        font-weight: 800;
                    }
                    p {
                        font-size: 1.5em;
                        margin: 10px 0;
                    }
                    .badge {
                        display: inline-block;
                        padding: 10px 25px;
                        background: #10b981;
                        border-radius: 25px;
                        font-weight: 700;
                        margin-top: 20px;
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <h1> TEST OK !</h1>
                    <div class="badge">Symfony fonctionne correctement</div>
                    <p>Date : ' . date('Y-m-d H:i:s') . '</p>
                    <p>Environment : Production</p>
                </div>
            </body>
            </html>
        ');
    }
}
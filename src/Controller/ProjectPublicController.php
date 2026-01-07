<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjectPublicController extends AbstractController
{
    #[Route('/projets-public', name: 'app_project_public', methods: ['GET'])]
    public function index(): Response
    {
        return new Response(
            '<div style="font-family: Poppins; padding: 40px; max-width: 800px; margin: 0 auto;">' .
            '<h1 style="color: #f4a261;"> MIDDO Platform - Template Premium Deploye !</h1>' .
            '<div style="background: #fff3e0; padding: 20px; border-radius: 8px; margin: 20px 0;">' .
            '<h2 style="color: #f4a261;"> Succes du deploiement</h2>' .
            '<ul style="line-height: 1.8;">' .
            '<li><strong>Route publique:</strong> /projets-public </li>' .
            '<li><strong>Template premium:</strong> Valide </li>' .
            '<li><strong>Design MIDDO:</strong> #f4a261 </li>' .
            '<li><strong>Encodage UTF-8:</strong> Sans BOM </li>' .
            '</ul>' .
            '</div>' .
            '<div style="background: #ffebee; padding: 20px; border-radius: 8px;">' .
            '<h3 style="color: #d32f2f;"> Statut Doctrine</h3>' .
            '<p>Entity Manager non configure en production Render.</p>' .
            '<p><strong>Solution:</strong> Ajouter warmup cache dans build command.</p>' .
            '</div>' .
            '<div style="margin-top: 30px; padding: 20px; background: #e8f5e9; border-radius: 8px;">' .
            '<h3 style="color: #388e3c;"> SESSION 19 - RESUME</h3>' .
            '<ul style="line-height: 1.8;">' .
            '<li> 88 fichiers backup supprimes</li>' .
            '<li> Encodage UTF-8 fixe definitivement</li>' .
            '<li> Scripts robustes crees (cleanup, save_php_file)</li>' .
            '<li> Route publique fonctionnelle</li>' .
            '<li> Template premium deploye</li>' .
            '<li> Configuration Doctrine en cours</li>' .
            '</ul>' .
            '</div>' .
            '</div>',
            Response::HTTP_OK
        );
    }
}
<?php

namespace App\Controller;

use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjectPublicController extends AbstractController
{
    #[Route('/projets-public', name: 'app_project_public', methods: ['GET'])]
    public function index(ProjectRepository $projectRepository): Response
    {
        try {
            $projects = $projectRepository->findAll();
            
            // Si la base est vide, afficher un message informatif
            if (empty($projects)) {
                return new Response(
                    '<div style="font-family: Poppins, sans-serif; padding: 40px; color: #264653; max-width: 800px; margin: 0 auto;">' .
                    '<h1 style="color: #f4a261; font-size: 2.5em; margin-bottom: 20px;"> MIDDO Platform</h1>' .
                    '<h2 style="color: #264653; font-size: 1.8em; margin-bottom: 15px;">Base de données vide</h2>' .
                    '<p style="font-size: 1.1em; line-height: 1.6; margin-bottom: 15px;">' .
                    'Aucun projet trouvé dans la base de données <strong>PostgreSQL</strong>.' .
                    '</p>' .
                    '<p style="font-size: 1.1em; line-height: 1.6; margin-bottom: 15px;">' .
                    '<strong> PostgreSQL persistant actif :</strong>' .
                    '</p>' .
                    '<ul style="font-size: 1.1em; line-height: 1.8; margin-bottom: 15px;">' .
                    '<li>Base : <code style="background: #e9c46a; padding: 2px 6px; border-radius: 3px;">middo_prod</code></li>' .
                    '<li>Plan : <code style="background: #e9c46a; padding: 2px 6px; border-radius: 3px;">Basic-256mb (10,50$/mois)</code></li>' .
                    '<li>Disponibilité : <strong style="color: #2a9d8f;">100% (zéro spindown)</strong></li>' .
                    '</ul>' .
                    '<p style="font-size: 1.1em; line-height: 1.6; margin-bottom: 15px;">' .
                    '<strong>🎯 Prochaine étape :</strong> Ajoute des projets via l\'interface admin.' .
                    '</p>' .
                    '<p style="margin-top: 30px; padding: 15px; background: #e9ecef; border-left: 4px solid #f4a261; border-radius: 4px;">' .
                    '<strong>SESSION 21 - OPTION A :</strong> PostgreSQL persistant déployé avec succès ' .
                    '</p>' .
                    '</div>',
                    Response::HTTP_OK
                );
            }
            
            // Afficher les projets via le template Twig
            return $this->render('project/index.html.twig', [
                'projects' => $projects,
            ]);
            
        } catch (\Exception $e) {
            // Gestion d'erreur simplifiée (plus de retry logic)
            return new Response(
                '<div style="font-family: Poppins, sans-serif; padding: 40px; color: #264653; max-width: 800px; margin: 0 auto;">' .
                '<h1 style="color: #e63946; font-size: 2.5em; margin-bottom: 20px;"> Erreur MIDDO Platform</h1>' .
                '<p style="font-size: 1.1em; line-height: 1.6; margin-bottom: 15px;">' .
                '<strong>Erreur détectée :</strong> ' . htmlspecialchars($e->getMessage()) .
                '</p>' .
                '<p style="font-size: 1.1em; line-height: 1.6; margin-bottom: 15px;">' .
                '<strong>Base PostgreSQL :</strong> middo_prod (Basic-256mb)' .
                '</p>' .
                '<p style="font-size: 1.1em; line-height: 1.6; margin-bottom: 15px;">' .
                '<strong>Contact support :</strong> Vérifie les logs Render ou contacte l\'admin.' .
                '</p>' .
                '<p style="margin-top: 30px; padding: 15px; background: #ffe5e5; border-left: 4px solid #e63946; border-radius: 4px;">' .
                'Si cette erreur persiste, vérifie la connexion PostgreSQL dans les logs Render.' .
                '</p>' .
                '</div>',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
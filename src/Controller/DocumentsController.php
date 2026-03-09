<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DocumentsController extends AbstractController
{
    #[Route('/documents', name: 'app_documents')]
    public function index(): Response
    {
        // Données temporaires (mock) en attendant la vraie base
        $documents = [
            [
                'id' => 1,
                'originalName' => 'Contrat de travail.pdf',
                'uploadedAt' => new \DateTime('-2 days'),
            ],
            [
                'id' => 2,
                'originalName' => 'Relevé bancaire janvier.xlsx',
                'uploadedAt' => new \DateTime('-5 days'),
            ],
            [
                'id' => 3,
                'originalName' => 'Dossier CAF.zip',
                'uploadedAt' => new \DateTime('-10 days'),
            ],
        ];

        return $this->render('documents/index.html.twig', [
            'documents' => $documents,
        ]);
    }
}

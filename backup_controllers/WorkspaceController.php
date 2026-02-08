<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WorkspaceController extends AbstractController
{
    #[Route('/workspace', name: 'workspace_index')]
    public function index(): Response
    {
        return $this->render('workspace/index.html.twig');
    }
}

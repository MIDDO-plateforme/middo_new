<?php

namespace App\Controller\IA;

use App\IA\History\HistoryManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/ia/history')]
final class HistoryPageController extends AbstractController
{
    #[Route('', name: 'ia_history_page', methods: ['GET'])]
    public function index(HistoryManager $history): Response
    {
        return $this->render('ia/history.html.twig', [
            'entries' => array_reverse($history->getAll()),
        ]);
    }
}

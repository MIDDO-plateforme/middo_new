<?php

namespace App\Controller\Document;

use App\Application\Document\DocumentAnalysisService;
use App\Domain\Document\Entity\UserDocument;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/documents')]
final class UserDocumentAnalysisController extends AbstractController
{
    #[Route('/{id}/analyze', name: 'user_documents_analyze')]
    public function analyze(
        string $id,
        EntityManagerInterface $em,
        DocumentAnalysisService $analysis
    ): Response {
        $doc = $em->getRepository(UserDocument::class)->find($id);

        if (!$doc || $doc->owner() !== $this->getUser()) {
            throw $this->createNotFoundException();
        }

        $result = $analysis->analyze($doc->filename());

        return $this->render('documents/analyze.html.twig', [
            'document' => $doc,
            'result' => $result,
        ]);
    }
}

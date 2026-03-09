<?php

namespace App\Controller\Document;

use App\Application\Document\DocumentStorageService;
use App\Domain\Document\Entity\UserDocument;
use App\Form\Document\UserDocumentUploadType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/documents')]
final class UserDocumentController extends AbstractController
{
    #[Route('', name: 'user_documents')]
    public function index(EntityManagerInterface $em): Response
    {
        $docs = $em->getRepository(UserDocument::class)
            ->findBy(['owner' => $this->getUser()]);

        return $this->render('documents/index.html.twig', [
            'documents' => $docs,
        ]);
    }

    #[Route('/upload', name: 'user_documents_upload')]
    public function upload(
        Request $request,
        DocumentStorageService $storage,
        EntityManagerInterface $em
    ): Response {
        $form = $this->createForm(UserDocumentUploadType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('file')->getData();
            $filename = $storage->store($file);

            $doc = new UserDocument(
                id: uuid_create(UUID_TYPE_RANDOM),
                owner: $this->getUser(),
                filename: $filename,
                originalName: $file->getClientOriginalName()
            );

            $em->persist($doc);
            $em->flush();

            return $this->redirectToRoute('user_documents');
        }

        return $this->render('documents/upload.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

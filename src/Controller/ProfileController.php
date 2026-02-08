<?php

namespace App\Controller;

use App\Form\ProfileEditType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(): Response
    {
        return $this->render('profile/index.html.twig');
    }

    #[Route('/profile/edit', name: 'app_profile_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfileEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'upload de la photo de profil
            $profilePictureFile = $form->get('profilePictureFile')->getData();
            
            if ($profilePictureFile) {
                // Supprimer l'ancienne photo si elle existe
                $oldPicture = $user->getProfilePicture();
                if ($oldPicture) {
                    $fileUploader->delete($oldPicture);
                }

                // Upload de la nouvelle photo (avec redimensionnement automatique)
                $profilePictureFileName = $fileUploader->upload($profilePictureFile);
                $user->setProfilePicture($profilePictureFileName);
            }

            $entityManager->flush();
            $this->addFlash('success', '✅ Votre profil a été mis à jour avec succès !');
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

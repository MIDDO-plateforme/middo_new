<?php

namespace App\Controller\User;

use App\Form\User\UserIaPreferencesType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/profile/ia')]
final class UserIaPreferencesController extends AbstractController
{
    #[Route('', name: 'app_profile_ia', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $prefs = $user->getIaPreferences();

        $form = $this->createForm(UserIaPreferencesType::class, $prefs);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setIaPreferences($form->getData());
            $em->flush();
            $this->addFlash('success', 'Préférences IA mises à jour.');
        }

        return $this->render('user/ia_preferences.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class LocaleController extends AbstractController
{
    #[Route('/change-locale/{locale}', name: 'change_locale')]
    public function changeLocale(string $locale, Request $request, EntityManagerInterface $em): RedirectResponse
    {
        // 1) Stocker en session
        $request->getSession()->set('_locale', $locale);

        // 2) Si utilisateur connecté → stocker en base
        if ($this->getUser()) {
            $this->getUser()->setLocale($locale);
            $em->flush();
        }

        // Retour à la page précédente
        $referer = $request->headers->get('referer');
        return new RedirectResponse($referer ?? '/');
    }
}

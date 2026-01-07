<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;

class LocaleSubscriber implements EventSubscriberInterface
{
    public function __construct(private Security $security) {}

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        // 1) Si l'utilisateur est connecté → priorité
        $user = $this->security->getUser();
        if ($user && $user->getLocale()) {
            $request->setLocale($user->getLocale());
            return;
        }

        // 2) Sinon → session
        if ($request->getSession()->has('_locale')) {
            $request->setLocale($request->getSession()->get('_locale'));
            return;
        }

        // 3) Sinon → langue du navigateur
        $preferred = $request->getPreferredLanguage([
            'en','fr','es','ar','sw','ln','wo','zh','id','hi','ur','pt'
        ]);

        $request->setLocale($preferred ?? 'en');
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }
}

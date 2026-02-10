<?php

namespace App\EventListener;

use App\Entity\Utilisateur;
use App\Service\LoginRedirectService;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

#[AsEventListener(event: LoginSuccessEvent::class)]
class LoginSuccessListener
{
    private LoginRedirectService $loginRedirectService;

    public function __construct(LoginRedirectService $loginRedirectService)
    {
        $this->loginRedirectService = $loginRedirectService;
    }

    public function __invoke(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();
        
        if ($user instanceof Utilisateur) {
            $redirectUrl = $this->loginRedirectService->getRedirectUrl($user);
            $response = new RedirectResponse($redirectUrl);
            $event->setResponse($response);
        }
    }
}
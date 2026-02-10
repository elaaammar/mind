<?php

namespace App\Service;

use App\Entity\Utilisateur;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LoginRedirectService
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function getRedirectUrl(Utilisateur $user): string
    {
        $roles = $user->getRoles();

        // Si l'utilisateur a le rôle administrateur, rediriger vers le dashboard
        if (in_array('ROLE_ADMINISTRATEUR', $roles)) {
            return $this->urlGenerator->generate('app_dashboard');
        }

        // Si l'utilisateur a le rôle auditeur, rediriger vers le dashboard aussi
        if (in_array('ROLE_AUDITEUR', $roles)) {
            return $this->urlGenerator->generate('app_dashboard');
        }

        // Pour tous les autres utilisateurs (ROLE_UTILISATEUR), rediriger vers l'espace utilisateur
        return $this->urlGenerator->generate('app_user_space');
    }
}
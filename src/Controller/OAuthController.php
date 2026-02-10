<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;

class OAuthController extends AbstractController
{
    #[Route('/connect/google', name: 'connect_google')]
    public function connectGoogle(ClientRegistry $clientRegistry): Response
    {
        return $clientRegistry
            ->getClient('google')
            ->redirect([
                'profile', 'email'
            ]);
    }

    #[Route('/connect/google/check', name: 'connect_google_check')]
    public function connectGoogleCheck(
        Request $request,
        ClientRegistry $clientRegistry,
        EntityManagerInterface $entityManager
    ): Response {
        $client = $clientRegistry->getClient('google');

        try {
            $user = $client->fetchUser();
            
            $email = $user->getEmail();
            $googleId = $user->getId();
            
            // Chercher l'utilisateur existant
            $utilisateur = $entityManager->getRepository(Utilisateur::class)
                ->findOneBy(['email' => $email]);
            
            if (!$utilisateur) {
                // Créer un nouvel utilisateur
                $utilisateur = new Utilisateur();
                $utilisateur->setEmail($email);
                $utilisateur->setNom($user->getLastName() ?? 'Google User');
                $utilisateur->setPrenom($user->getFirstName() ?? '');
                $utilisateur->setOauthProvider('google');
                $utilisateur->setOauthId($googleId);
                $utilisateur->setAvatar($user->getAvatar());
                $utilisateur->setActif(true);
                
                // Assigner le rôle par défaut "Utilisateur"
                $defaultRole = $entityManager->getRepository(Role::class)
                    ->findOneBy(['nom' => 'Utilisateur']);
                
                if ($defaultRole) {
                    $utilisateur->setRole($defaultRole);
                }
                
                $entityManager->persist($utilisateur);
                $entityManager->flush();
                
                $this->addFlash('success', 'Votre compte a été créé avec succès via Google !');
            } else {
                // Mettre à jour les informations OAuth si nécessaire
                if (!$utilisateur->getOauthProvider()) {
                    $utilisateur->setOauthProvider('google');
                    $utilisateur->setOauthId($googleId);
                    $utilisateur->setAvatar($user->getAvatar());
                    $entityManager->flush();
                }
                
                $this->addFlash('success', 'Connexion réussie via Google !');
            }
            
            // Rediriger vers la page de connexion pour que Symfony authentifie l'utilisateur
            return $this->redirectToRoute('app_login', [
                'oauth_email' => $email
            ]);
            
        } catch (IdentityProviderException $e) {
            $this->addFlash('error', 'Erreur lors de la connexion avec Google : ' . $e->getMessage());
            return $this->redirectToRoute('app_login');
        }
    }

    #[Route('/connect/facebook', name: 'connect_facebook')]
    public function connectFacebook(ClientRegistry $clientRegistry): Response
    {
        return $clientRegistry
            ->getClient('facebook')
            ->redirect([
                'public_profile', 'email'
            ]);
    }

    #[Route('/connect/facebook/check', name: 'connect_facebook_check')]
    public function connectFacebookCheck(
        Request $request,
        ClientRegistry $clientRegistry,
        EntityManagerInterface $entityManager
    ): Response {
        $client = $clientRegistry->getClient('facebook');

        try {
            $user = $client->fetchUser();
            
            $email = $user->getEmail();
            $facebookId = $user->getId();
            
            // Chercher l'utilisateur existant
            $utilisateur = $entityManager->getRepository(Utilisateur::class)
                ->findOneBy(['email' => $email]);
            
            if (!$utilisateur) {
                // Créer un nouvel utilisateur
                $utilisateur = new Utilisateur();
                $utilisateur->setEmail($email);
                
                // Facebook retourne le nom complet
                $name = $user->getName();
                $nameParts = explode(' ', $name, 2);
                $utilisateur->setPrenom($nameParts[0] ?? 'Facebook');
                $utilisateur->setNom($nameParts[1] ?? 'User');
                
                $utilisateur->setOauthProvider('facebook');
                $utilisateur->setOauthId($facebookId);
                $utilisateur->setAvatar($user->getPictureUrl());
                $utilisateur->setActif(true);
                
                // Assigner le rôle par défaut "Utilisateur"
                $defaultRole = $entityManager->getRepository(Role::class)
                    ->findOneBy(['nom' => 'Utilisateur']);
                
                if ($defaultRole) {
                    $utilisateur->setRole($defaultRole);
                }
                
                $entityManager->persist($utilisateur);
                $entityManager->flush();
                
                $this->addFlash('success', 'Votre compte a été créé avec succès via Facebook !');
            } else {
                // Mettre à jour les informations OAuth si nécessaire
                if (!$utilisateur->getOauthProvider()) {
                    $utilisateur->setOauthProvider('facebook');
                    $utilisateur->setOauthId($facebookId);
                    $utilisateur->setAvatar($user->getPictureUrl());
                    $entityManager->flush();
                }
                
                $this->addFlash('success', 'Connexion réussie via Facebook !');
            }
            
            // Rediriger vers la page de connexion pour que Symfony authentifie l'utilisateur
            return $this->redirectToRoute('app_login', [
                'oauth_email' => $email
            ]);
            
        } catch (IdentityProviderException $e) {
            $this->addFlash('error', 'Erreur lors de la connexion avec Facebook : ' . $e->getMessage());
            return $this->redirectToRoute('app_login');
        }
    }
}

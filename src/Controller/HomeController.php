<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Repository\RoleRepository;
use App\Repository\UtilisateurRepository;
use App\Service\LoginRedirectService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(LoginRedirectService $loginRedirectService): Response
    {
        // Si l'utilisateur est connecté, le rediriger vers son espace approprié
        if ($this->getUser() instanceof Utilisateur) {
            $user = $this->getUser();
            
            // Debug: afficher les informations de l'utilisateur
            // dump($user->getRoles());
            // dump($user->getRole());
            
            $redirectUrl = $loginRedirectService->getRedirectUrl($user);
            return $this->redirect($redirectUrl);
        }

        // Si l'utilisateur n'est pas connecté, rediriger vers la page de connexion
        return $this->redirectToRoute('app_login');
    }

    #[Route('/dashboard', name: 'app_dashboard')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function dashboard(
        UtilisateurRepository $utilisateurRepository,
        RoleRepository $roleRepository,
        LoginRedirectService $loginRedirectService
    ): Response {
        // Vérifier si l'utilisateur a le bon rôle pour accéder au dashboard
        $user = $this->getUser();
        if ($user instanceof Utilisateur) {
            $roles = $user->getRoles();
            
            // Si l'utilisateur n'est ni admin ni auditeur, le rediriger vers son espace
            if (!in_array('ROLE_ADMINISTRATEUR', $roles) && !in_array('ROLE_AUDITEUR', $roles)) {
                $redirectUrl = $loginRedirectService->getRedirectUrl($user);
                return $this->redirect($redirectUrl);
            }
        }

        $totalUtilisateurs = count($utilisateurRepository->findAll());
        $utilisateursActifs = $utilisateurRepository->countActifs();
        $totalRoles = count($roleRepository->findAll());
        $statsRoles = $roleRepository->countUtilisateursParRole();

        return $this->render('home/dashboard.html.twig', [
            'totalUtilisateurs' => $totalUtilisateurs,
            'utilisateursActifs' => $utilisateursActifs,
            'totalRoles' => $totalRoles,
            'statsRoles' => $statsRoles,
        ]);
    }
}

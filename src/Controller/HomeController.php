<?php

namespace App\Controller;

use App\Entity\Report;
use App\Entity\Utilisateur;
use App\Form\ReportType;
use App\Repository\RecommendationRepository;
use App\Repository\ReportRepository;
use App\Repository\RoleRepository;
use App\Repository\UtilisateurRepository;
use App\Service\LoginRedirectService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, ReportRepository $reportRepository, RecommendationRepository $recommendationRepository, EntityManagerInterface $entityManager, LoginRedirectService $loginRedirectService): Response
    {
        // Si l'utilisateur est déjà connecté et qu'on veut le rediriger (optionnel)
        // if ($this->getUser() instanceof Utilisateur) {
        //     return $this->redirect($loginRedirectService->getRedirectUrl($this->getUser()));
        // }

        $report = new Report();
        $report->setStatus('En cours');
        $report->setPriority('Moyenne');
        $report->setScore(0);
        $report->setSource('user');
        
        $form = $this->createForm(ReportType::class, $report);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($report);
            $entityManager->flush();

            $this->addFlash('success', 'Votre rapport a été créé avec succès.');

            return $this->redirectToRoute('app_user_dashboard');
        }

        $latestReports = $reportRepository->findBy(['status' => 'Validé'], ['id' => 'DESC'], 3);
        $latestRecommendations = $recommendationRepository->findByReportStatus('Validé', 3);

        return $this->render('home/index.html.twig', [
            'latest_reports' => $latestReports,
            'latest_recommendations' => $latestRecommendations,
            'report_form' => $form->createView(),
        ]);
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

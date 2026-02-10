<?php

namespace App\Controller;

use App\Repository\ReclamationRepository;
use App\Repository\ReponseReclamationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(ReclamationRepository $reclamationRepository, ReponseReclamationRepository $reponseReclamationRepository): Response
    {
        $totalReclamations = $reclamationRepository->count([]);
        $totalReponses = $reponseReclamationRepository->count([]);

        $byStatut = $reclamationRepository->countByStatut();
        $byPriorite = $reclamationRepository->countByPriorite();

        $reclamationsByDate = $reclamationRepository->countByDate(30);
        $reponsesByDate = $reponseReclamationRepository->countByDate(30);

        $reponsesByAuteurType = $reponseReclamationRepository->countByAuteurType();

        return $this->render('dashboard/index.html.twig', [
            'totalReclamations' => $totalReclamations,
            'totalReponses' => $totalReponses,
            'byStatut' => $byStatut,
            'byPriorite' => $byPriorite,
            'reclamationsByDate' => $reclamationsByDate,
            'reponsesByDate' => $reponsesByDate,
            'reponsesByAuteurType' => $reponsesByAuteurType,
        ]);
    }
}

<?php

namespace App\Controller;

use App\Repository\ReclamationRepository;
use App\Repository\ReponseReclamationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/stats')]
final class StatController extends AbstractController
{
    #[Route('', name: 'app_stat_index', methods: ['GET'])]
    public function index(
        ReclamationRepository $reclamationRepository,
        ReponseReclamationRepository $reponseReclamationRepository
    ): Response {
        $totalReclamations = $reclamationRepository->count([]);
        $totalReponses = $reponseReclamationRepository->count([]);
        $pendingReclamations = $reclamationRepository->count(['statut' => 'en_attente']);

        $reclamationsByStatut = $reclamationRepository->countByStatut();
        $reclamationsByPriorite = $reclamationRepository->countByPriorite();

        return $this->render('stat/index.html.twig', [
            'total_reclamations' => $totalReclamations,
            'total_reponses' => $totalReponses,
            'pending_reclamations' => $pendingReclamations,
            'reclamations_by_statut' => $reclamationsByStatut,
            'reclamations_by_priorite' => $reclamationsByPriorite,
        ]);
    }
}

<?php

namespace App\Controller;

use App\Repository\AuditRepository;
use App\Repository\QuestionAuditRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractController
{
    #[Route('/admin', name: 'app_dashboard')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(AuditRepository $auditRepository, QuestionAuditRepository $questionRepository, UserRepository $userRepository): Response
    {
        return $this->render('dashboard/index.html.twig', [
            'audit_count' => $auditRepository->count([]),
            'question_count' => $questionRepository->count([]),
            'user_count' => $userRepository->count([]),
            'public_audit_count' => $auditRepository->count(['status' => 'public']),
        ]);
    }
}

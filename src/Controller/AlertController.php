<?php

namespace App\Controller;

use App\Repository\AlertRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AlertController extends AbstractController
{
    #[Route('/alerts/dropdown', name: 'app_alerts_dropdown')]
    public function dropdown(AlertRepository $alertRepository): Response
    {
        $unreadAlerts = $alertRepository->findBy(['isRead' => false], ['createdAt' => 'DESC'], 5);
        $totalUnread = count($alertRepository->findBy(['isRead' => false]));

        return $this->render('_alerts_dropdown.html.twig', [
            'alerts' => $unreadAlerts,
            'totalUnread' => $totalUnread
        ]);
    }

    #[Route('/alerts/read/{id}', name: 'app_alert_mark_read')]
    public function markAsRead(int $id, \App\Repository\AlertRepository $alertRepository, \Doctrine\ORM\EntityManagerInterface $em): Response
    {
        $alert = $alertRepository->find($id);
        if ($alert) {
            $alert->setRead(true);
            $em->flush();
        }

        return $this->json(['success' => true]);
    }
}

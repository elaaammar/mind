<?php

namespace App\Controller;

use App\Repository\RecommendationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class NotificationController extends AbstractController
{
    public function unreadCount(RecommendationRepository $recommendationRepository): Response
    {
        // Count unread recommendations where the linked report's source is 'user'
        $unreadCount = $recommendationRepository->createQueryBuilder('r')
            ->select('count(r.id)')
            ->join('r.report', 'rep')
            ->where('r.readByUser = :read')
            ->andWhere('rep.source = :source')
            ->setParameter('read', false)
            ->setParameter('source', 'user')
            ->getQuery()
            ->getSingleScalarResult();

        return new Response((string)$unreadCount);
    }

    public function unreadList(RecommendationRepository $recommendationRepository): Response
    {
        // Get last 5 unread recommendations for user reports
        $unread = $recommendationRepository->createQueryBuilder('r')
            ->join('r.report', 'rep')
            ->where('r.readByUser = :read')
            ->andWhere('rep.source = :source')
            ->setParameter('read', false)
            ->setParameter('source', 'user')
            ->orderBy('r.createdAt', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        return $this->render('_notifications.html.twig', [
            'notifications' => $unread
        ]);
    }
}

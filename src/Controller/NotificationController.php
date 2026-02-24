<?php

namespace App\Controller;

use App\Repository\RecommendationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

#[AsController]
class NotificationController extends AbstractController
{
    #[Route('/notifications/read/{id}', name: 'app_notification_mark_read')]
    public function markAsRead(int $id, \App\Repository\RecommendationRepository $recommendationRepository, EntityManagerInterface $em): Response
    {
        $rec = $recommendationRepository->find($id);
        if ($rec) {
            $rec->setReadByUser(true);
            $em->flush();
        }
        return $this->json(['success' => true]);
    }

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
        $qb = $recommendationRepository->createQueryBuilder('r')
            ->join('r.report', 'rep')
            ->where('r.readByUser = :read')
            ->andWhere('rep.source = :source')
            ->setParameter('read', false)
            ->setParameter('source', 'user')
            ->orderBy('r.createdAt', 'DESC');

        $unread = (clone $qb)->setMaxResults(5)->getQuery()->getResult();
        $totalUnread = (clone $qb)->select('count(r.id)')->getQuery()->getSingleScalarResult();

        return $this->render('_notifications.html.twig', [
            'notifications' => $unread,
            'totalUnread' => $totalUnread
        ]);
    }

}

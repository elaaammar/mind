<?php

namespace App\Controller;

use App\Entity\Report;
use App\Entity\ReportVersion;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/report')]
class ReportHistoryController extends AbstractController
{
    #[Route('/{id}/history', name: 'app_report_history')]
    public function history(Report $report, EntityManagerInterface $em): Response
    {
        $logs = $em->getRepository(ReportVersion::class)
            ->findBy(['report' => $report], ['version' => 'DESC']);

        return $this->render('report/history.html.twig', [
            'report' => $report,
            'logs'   => $logs,
        ]);
    }
}

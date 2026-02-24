<?php

namespace App\Controller;

use App\Repository\ReportRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/export')]
class ExportController extends AbstractController
{
    #[Route('/csv', name: 'app_export_csv')]
    public function exportCsv(ReportRepository $reportRepository): Response
    {
        $reports = $reportRepository->findAll();
        
        $fp = fopen('php://temp', 'r+');
        
        // UTF-8 BOM for Excel
        fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Header
        fputcsv($fp, ['ID', 'Titre', 'Type', 'Statut', 'Priorité', 'Score', 'Date de création'], ';');
        
        foreach ($reports as $report) {
            fputcsv($fp, [
                $report->getId(),
                $report->getTitle(),
                $report->getType(),
                $report->getStatus(),
                $report->getPriority(),
                $report->getScore() . '%',
                $report->getCreatedAt()->format('d/m/Y H:i')
            ], ';');
        }
        
        rewind($fp);
        $csv = stream_get_contents($fp);
        fclose($fp);
        
        $response = new Response($csv);
        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            'export_rapports_' . date('Y-m-d') . '.csv'
        );
        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        
        return $response;
    }

    #[Route('/excel', name: 'app_export_excel')]
    public function exportExcel(ReportRepository $reportRepository): Response
    {
        $reports = $reportRepository->findAll();
        
        // Simple HTML Table export for Excel
        $html = '
        <table border="1">
            <tr style="background-color: #4e73df; color: white; font-weight: bold;">
                <th>ID</th>
                <th>Titre</th>
                <th>Type</th>
                <th>Statut</th>
                <th>Priorit&eacute;</th>
                <th>Score</th>
                <th>Date de cr&eacute;ation</th>
            </tr>';
        
        foreach ($reports as $report) {
            $html .= sprintf(
                '<tr>
                    <td>%d</td>
                    <td>%s</td>
                    <td>%s</td>
                    <td>%s</td>
                    <td>%s</td>
                    <td>%d%%</td>
                    <td>%s</td>
                </tr>',
                $report->getId(),
                htmlspecialchars($report->getTitle()),
                htmlspecialchars($report->getType()),
                htmlspecialchars($report->getStatus()),
                htmlspecialchars($report->getPriority()),
                $report->getScore(),
                $report->getCreatedAt()->format('d/m/Y H:i')
            );
        }
        $html .= '</table>';
        
        $response = new Response($html);
        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            'export_rapports_' . date('Y-m-d') . '.xls'
        );
        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        
        return $response;
    }
}

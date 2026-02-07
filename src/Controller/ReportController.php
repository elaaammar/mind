<?php

namespace App\Controller;

use App\Entity\Report;
use App\Form\ReportType;
use App\Repository\ReportRepository;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/report')]
final class ReportController extends AbstractController
{
    #[Route('/{id}/pdf', name: 'app_report_pdf', methods: ['GET'])]
    public function downloadPdf(Report $report): Response
    {
        // Configure Dompdf
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->set('isRemoteEnabled', true);
        
        $dompdf = new Dompdf($pdfOptions);
        
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('report/pdf.html.twig', [
            'report' => $report
        ]);
        
        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        
        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'portrait');
        
        // Render the HTML as PDF
        $dompdf->render();
        
        // Generate PDF content
        $output = $dompdf->output();
        
        // Create a Response object
        $response = new Response($output);
        
        // Set headers for PDF download
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="rapport_audit_' . $report->getId() . '.pdf"');

        return $response;
    }

    #[Route(name: 'app_report_index', methods: ['GET'])]
    public function index(Request $request, ReportRepository $reportRepository): Response
    {
        $query = $request->query->get('q');

        if ($query) {
            $reports = $reportRepository->findBySearch($query);
        } else {
            $reports = $reportRepository->findAll();
        }

        return $this->render('report/index.html.twig', [
            'reports' => $reports,
        ]);
    }

    #[Route('/new', name: 'app_report_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $report = new Report();
        $form = $this->createForm(ReportType::class, $report);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($report);
            $entityManager->flush();

            if ($report->getPriority() === 'Forte') {
                $this->addFlash('danger', 'Attention : Un rapport de priorité FORTE a été ajouté !');
            } else {
                $this->addFlash('success', 'Le rapport a été créé avec succès.');
            }

            return $this->redirectToRoute('app_report_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('report/new.html.twig', [
            'report' => $report,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_report_show', methods: ['GET'])]
    public function show(Report $report): Response
    {
        return $this->render('report/show.html.twig', [
            'report' => $report,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_report_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Report $report, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReportType::class, $report);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Le rapport a été mis à jour avec succès.');

            return $this->redirectToRoute('app_report_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('report/edit.html.twig', [
            'report' => $report,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_report_delete', methods: ['POST'])]
    public function delete(Request $request, Report $report, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$report->getId(), $request->request->get('_token'))) {
            $entityManager->remove($report);
            $entityManager->flush();
            $this->addFlash('success', 'Le rapport a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_report_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/{id}/recommend', name: 'app_report_recommend', methods: ['POST'])]
    public function recommend(Report $report, EntityManagerInterface $entityManager): Response
    {
        $score = $report->getScore();
        $priority = $report->getPriority();
        $content = "Analyse générée le " . date('d/m/Y H:i') . " :\n\n";

        // Logic based on Score and Priority
        if ($score < 50) {
            $content .= "URGENT : Le score de conformité est critique ($score/100). ";
            $content .= "Il est impératif de revoir l'ensemble des points de contrôle défaillants. ";
            if ($priority === 'Forte') {
                $content .= "Compte tenu de la priorité FORTE, une réunion de crise doit être planifiée immédiatement.";
            }
        } elseif ($score < 80) {
            $content .= "ATTENTION : Le score est moyen ($score/100). ";
            $content .= "Certains aspects nécessitent une correction rapide pour éviter une dégradation. ";
            $content .= "Veuillez consulter les annexes techniques.";
        } else {
            $content .= "EXCELLENT : Le rapport indique un bon niveau de conformité ($score/100). ";
            $content .= "Maintenez les efforts actuels. Une simple revue périodique est recommandée.";
        }

        $recommendation = new \App\Entity\Recommendation();
        $recommendation->setContent($content);
        $recommendation->setReport($report);
        
        $entityManager->persist($recommendation);
        $entityManager->flush();

        $this->addFlash('success', 'La recommandation a été générée avec succès.');

        return $this->redirectToRoute('app_report_show', ['id' => $report->getId()]);
    }
}

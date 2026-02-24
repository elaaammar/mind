<?php

namespace App\Controller;

use App\Entity\ReponseReclamation;
use App\Form\ReponseReclamationType;
use App\Repository\ReclamationRepository;
use App\Repository\ReponseReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;
use Dompdf\Dompdf;
use Dompdf\Options;

#[Route('/reponse/reclamation')]
final class ReponseReclamationController extends AbstractController
{
    private const PAGE_SIZE_OPTIONS = [10, 25, 50];
    private const DEFAULT_PAGE_SIZE = 10;

    #[Route(name: 'app_reponse_reclamation_index', methods: ['GET'])]
    public function index(
        Request $request,
        ReponseReclamationRepository $reponseReclamationRepository,
        ReclamationRepository $reclamationRepository,
        PaginatorInterface $paginator
    ): Response {
        $pageSize = (int) $request->query->get('per_page', self::DEFAULT_PAGE_SIZE);
        if (!\in_array($pageSize, self::PAGE_SIZE_OPTIONS, true)) {
            $pageSize = self::DEFAULT_PAGE_SIZE;
        }

        $qb = $this->buildFilteredQuery($request, $reponseReclamationRepository);
        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            $pageSize
        );

        $reclamations = $reclamationRepository->findAll();

        $filterParams = $this->getFilterParams($request);
        $exportQuery = http_build_query(array_filter($filterParams));

        return $this->render('reponse_reclamation/index.html.twig', [
            'pagination' => $pagination,
            'reclamations' => $reclamations,
            'page_size_options' => self::PAGE_SIZE_OPTIONS,
            'current_page_size' => $pageSize,
            'filter_params' => $filterParams,
            'export_query' => $exportQuery,
        ]);
    }

    /**
     * AJAX: returns table body rows + pagination HTML (for live search/filter without full page reload).
     */
    #[Route('/list-ajax', name: 'app_reponse_reclamation_list_ajax', methods: ['GET'])]
    public function listAjax(
        Request $request,
        ReponseReclamationRepository $reponseReclamationRepository,
        PaginatorInterface $paginator
    ): Response {
        $pageSize = (int) $request->query->get('per_page', self::DEFAULT_PAGE_SIZE);
        if (!\in_array($pageSize, self::PAGE_SIZE_OPTIONS, true)) {
            $pageSize = self::DEFAULT_PAGE_SIZE;
        }

        $qb = $this->buildFilteredQuery($request, $reponseReclamationRepository);
        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            $pageSize
        );

        $htmlRows = $this->renderView('reponse_reclamation/_table_rows.html.twig', [
            'pagination' => $pagination,
        ]);
        $htmlPagination = $this->renderView('reponse_reclamation/_pagination.html.twig', [
            'pagination' => $pagination,
            'page_size_options' => self::PAGE_SIZE_OPTIONS,
            'current_page_size' => $pageSize,
            'filter_params' => $this->getFilterParams($request),
        ]);

        return new JsonResponse([
            'rows' => $htmlRows,
            'pagination' => $htmlPagination,
            'total' => $pagination->getTotalItemCount(),
        ]);
    }

    #[Route('/new', name: 'app_reponse_reclamation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reponseReclamation = new ReponseReclamation();
        $form = $this->createForm(ReponseReclamationType::class, $reponseReclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reponseReclamation);
            $entityManager->flush();

            $this->addFlash('success', 'Réponse créée avec succès.');
            return $this->redirectToRoute('app_reponse_reclamation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reponse_reclamation/new.html.twig', [
            'reponse_reclamation' => $reponseReclamation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reponse_reclamation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ReponseReclamation $reponseReclamation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReponseReclamationType::class, $reponseReclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Réponse mise à jour avec succès.');
            return $this->redirectToRoute('app_reponse_reclamation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reponse_reclamation/edit.html.twig', [
            'reponse_reclamation' => $reponseReclamation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reponse_reclamation_show', methods: ['GET'])]
    public function show(ReponseReclamation $reponseReclamation): Response
    {
        return $this->render('reponse_reclamation/show.html.twig', [
            'reponse_reclamation' => $reponseReclamation,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_reponse_reclamation_delete', methods: ['POST'])]
    public function delete(Request $request, ReponseReclamation $reponseReclamation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $reponseReclamation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reponseReclamation);
            $entityManager->flush();
            $this->addFlash('success', 'Réponse supprimée.');
        }

        return $this->redirectToRoute('app_reponse_reclamation_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/export/pdf', name: 'app_reponse_reclamation_export_pdf', methods: ['GET'])]
    public function exportPdf(Request $request, ReponseReclamationRepository $reponseReclamationRepository): Response
    {
        $qb = $this->buildFilteredQuery($request, $reponseReclamationRepository);
        $qb->setMaxResults(500);
        $items = $qb->getQuery()->getResult();

        $html = $this->renderView('reponse_reclamation/export_pdf.html.twig', [
            'reponse_reclamations' => $items,
        ]);

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="reponses-reclamation-' . date('Y-m-d') . '.pdf"',
        ]);
    }

    #[Route('/export/csv', name: 'app_reponse_reclamation_export_csv', methods: ['GET'])]
    public function exportCsv(Request $request, ReponseReclamationRepository $reponseReclamationRepository): StreamedResponse
    {
        $qb = $this->buildFilteredQuery($request, $reponseReclamationRepository);
        $items = $qb->getQuery()->getResult();

        $response = new StreamedResponse(function () use ($items) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['ID', 'Réclamation (ID)', 'Titre réclamation', 'Contenu', 'Nom', 'Type auteur', 'Date création'], ';');
            foreach ($items as $rr) {
                $r = $rr->getReclamation();
                fputcsv($handle, [
                    $rr->getId(),
                    $r ? $r->getId() : '',
                    $r ? $r->getTitre() : '',
                    $rr->getContenu(),
                    $rr->getNom(),
                    $rr->getAuteurType(),
                    $rr->getDateCreation() ? $rr->getDateCreation()->format('Y-m-d H:i') : '',
                ], ';');
            }
            fclose($handle);
        });
        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="reponses-reclamation-' . date('Y-m-d') . '.csv"');
        return $response;
    }

    private function buildFilteredQuery(Request $request, ReponseReclamationRepository $repo): \Doctrine\ORM\QueryBuilder
    {
        $search = $request->query->get('search');
        $reclamationId = $request->query->getInt('reclamation_id') ?: null;
        $dateFrom = $request->query->get('date_from') ? \DateTime::createFromFormat('Y-m-d', $request->query->get('date_from')) : null;
        $dateTo = $request->query->get('date_to') ? \DateTime::createFromFormat('Y-m-d', $request->query->get('date_to')) : null;
        if ($dateTo) {
            $dateTo->setTime(23, 59, 59);
        }
        $auteurType = $request->query->get('auteur_type');

        return $repo->createQueryBuilderWithFilters($search, $reclamationId, $dateFrom, $dateTo, $auteurType);
    }

    private function getFilterParams(Request $request): array
    {
        return [
            'search' => $request->query->get('search', ''),
            'reclamation_id' => $request->query->get('reclamation_id', ''),
            'date_from' => $request->query->get('date_from', ''),
            'date_to' => $request->query->get('date_to', ''),
            'auteur_type' => $request->query->get('auteur_type', ''),
        ];
    }
}
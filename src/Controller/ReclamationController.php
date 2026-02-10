<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;
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

#[Route('/reclamation')]
final class ReclamationController extends AbstractController
{
    private const PAGE_SIZE_OPTIONS = [10, 25, 50];
    private const DEFAULT_PAGE_SIZE = 10;

    #[Route(name: 'app_reclamation_index', methods: ['GET'])]
    public function index(
        Request $request,
        ReclamationRepository $reclamationRepository,
        PaginatorInterface $paginator
    ): Response {
        $pageSize = (int) $request->query->get('per_page', self::DEFAULT_PAGE_SIZE);
        if (!\in_array($pageSize, self::PAGE_SIZE_OPTIONS, true)) {
            $pageSize = self::DEFAULT_PAGE_SIZE;
        }

        $qb = $this->buildFilteredQuery($request, $reclamationRepository);
        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            $pageSize
        );

        $categories = $reclamationRepository->findDistinctCategories();

        $filterParams = $this->getFilterParams($request);
        $exportQuery = http_build_query(array_filter($filterParams));

        return $this->render('reclamation/index.html.twig', [
            'pagination' => $pagination,
            'categories' => $categories,
            'page_size_options' => self::PAGE_SIZE_OPTIONS,
            'current_page_size' => $pageSize,
            'filter_params' => $filterParams,
            'export_query' => $exportQuery,
        ]);
    }

    /**
     * AJAX: returns table body rows + pagination HTML (for live search/filter without full page reload).
     */
    #[Route('/list-ajax', name: 'app_reclamation_list_ajax', methods: ['GET'])]
    public function listAjax(
        Request $request,
        ReclamationRepository $reclamationRepository,
        PaginatorInterface $paginator
    ): Response {
        $pageSize = (int) $request->query->get('per_page', self::DEFAULT_PAGE_SIZE);
        if (!\in_array($pageSize, self::PAGE_SIZE_OPTIONS, true)) {
            $pageSize = self::DEFAULT_PAGE_SIZE;
        }

        $qb = $this->buildFilteredQuery($request, $reclamationRepository);
        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            $pageSize
        );

        $htmlRows = $this->renderView('reclamation/_table_rows.html.twig', [
            'pagination' => $pagination,
        ]);
        $htmlPagination = $this->renderView('reclamation/_pagination.html.twig', [
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

    #[Route('/new', name: 'app_reclamation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reclamation = new Reclamation();
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reclamation);
            $entityManager->flush();

            $this->addFlash('success', 'Réclamation créée avec succès.');
            return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reclamation/new.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reclamation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Réclamation mise à jour avec succès.');
            return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reclamation/edit.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reclamation_show', methods: ['GET'])]
    public function show(Reclamation $reclamation): Response
    {
        return $this->render('reclamation/show.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_reclamation_delete', methods: ['POST'])]
    public function delete(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $reclamation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reclamation);
            $entityManager->flush();
            $this->addFlash('success', 'Réclamation supprimée.');
        }

        return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/export/pdf', name: 'app_reclamation_export_pdf', methods: ['GET'])]
    public function exportPdf(Request $request, ReclamationRepository $reclamationRepository): Response
    {
        $qb = $this->buildFilteredQuery($request, $reclamationRepository);
        $qb->setMaxResults(500);
        $reclamations = $qb->getQuery()->getResult();

        $html = $this->renderView('reclamation/export_pdf.html.twig', [
            'reclamations' => $reclamations,
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
            'Content-Disposition' => 'attachment; filename="reclamations-' . date('Y-m-d') . '.pdf"',
        ]);
    }

    #[Route('/export/csv', name: 'app_reclamation_export_csv', methods: ['GET'])]
    public function exportCsv(Request $request, ReclamationRepository $reclamationRepository): StreamedResponse
    {
        $qb = $this->buildFilteredQuery($request, $reclamationRepository);
        $reclamations = $qb->getQuery()->getResult();

        $response = new StreamedResponse(function () use ($reclamations) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['ID', 'Titre', 'Description', 'Date création', 'Statut', 'Priorité', 'Catégorie', 'Nom', 'Email', 'Téléphone'], ';');
            foreach ($reclamations as $r) {
                fputcsv($handle, [
                    $r->getId(),
                    $r->getTitre(),
                    $r->getDescription(),
                    $r->getDateCreation() ? $r->getDateCreation()->format('Y-m-d H:i') : '',
                    $r->getStatut(),
                    $r->getPriorite(),
                    $r->getCategorie(),
                    $r->getNom(),
                    $r->getEmail(),
                    $r->getTelephone(),
                ], ';');
            }
            fclose($handle);
        });
        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="reclamations-' . date('Y-m-d') . '.csv"');
        return $response;
    }

    private function buildFilteredQuery(Request $request, ReclamationRepository $repo): \Doctrine\ORM\QueryBuilder
    {
        $search = $request->query->get('search');
        $statut = $request->query->get('statut');
        $priorite = $request->query->get('priorite');
        $categorie = $request->query->get('categorie');
        $dateFrom = $request->query->get('date_from') ? \DateTime::createFromFormat('Y-m-d', $request->query->get('date_from')) : null;
        $dateTo = $request->query->get('date_to') ? \DateTime::createFromFormat('Y-m-d', $request->query->get('date_to')) : null;
        if ($dateTo) {
            $dateTo->setTime(23, 59, 59);
        }

        return $repo->createQueryBuilderWithFilters($search, $statut, $priorite, $categorie, $dateFrom, $dateTo);
    }

    private function getFilterParams(Request $request): array
    {
        return [
            'search' => $request->query->get('search', ''),
            'statut' => $request->query->get('statut', ''),
            'priorite' => $request->query->get('priorite', ''),
            'categorie' => $request->query->get('categorie', ''),
            'date_from' => $request->query->get('date_from', ''),
            'date_to' => $request->query->get('date_to', ''),
        ];
    }
}
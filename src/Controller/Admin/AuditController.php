<?php

namespace App\Controller\Admin;

use App\Entity\Audit;
use App\Form\AuditType;
use App\Repository\AuditRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/audit')]
class AuditController extends AbstractController
{
    #[Route('/', name: 'app_admin_audit_index', methods: ['GET'])]
    public function index(AuditRepository $auditRepository): Response
    {
        return $this->render('admin/audit/index.html.twig', [
            'audits' => $auditRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_audit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $audit = new Audit();
        $form = $this->createForm(AuditType::class, $audit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($audit);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_audit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/audit/new.html.twig', [
            'audit' => $audit,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_admin_audit_show', methods: ['GET'])]
    public function show(Audit $audit): Response
    {
        return $this->render('admin/audit/show.html.twig', [
            'audit' => $audit,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_audit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Audit $audit, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AuditType::class, $audit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_audit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/audit/edit.html.twig', [
            'audit' => $audit,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_admin_audit_delete', methods: ['POST'])]
    public function delete(Request $request, Audit $audit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$audit->getId(), $request->request->get('_token'))) {
            $entityManager->remove($audit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_audit_index', [], Response::HTTP_SEE_OTHER);
    }
}

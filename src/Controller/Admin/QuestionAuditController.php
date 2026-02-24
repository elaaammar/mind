<?php

namespace App\Controller\Admin;

use App\Entity\QuestionAudit;
use App\Form\QuestionAuditType;
use App\Repository\QuestionAuditRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/question')]
class QuestionAuditController extends AbstractController
{
    #[Route('/', name: 'app_admin_question_index', methods: ['GET'])]
    public function index(QuestionAuditRepository $questionAuditRepository): Response
    {
        return $this->render('admin/question_audit/index.html.twig', [
            'questions' => $questionAuditRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_question_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $questionAudit = new QuestionAudit();
        $form = $this->createForm(QuestionAuditType::class, $questionAudit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($questionAudit);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_question_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/question_audit/new.html.twig', [
            'question_audit' => $questionAudit,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_admin_question_show', methods: ['GET'])]
    public function show(QuestionAudit $questionAudit): Response
    {
        return $this->render('admin/question_audit/show.html.twig', [
            'question_audit' => $questionAudit,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_question_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, QuestionAudit $questionAudit, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(QuestionAuditType::class, $questionAudit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_question_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/question_audit/edit.html.twig', [
            'question_audit' => $questionAudit,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_admin_question_delete', methods: ['POST'])]
    public function delete(Request $request, QuestionAudit $questionAudit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$questionAudit->getId(), $request->request->get('_token'))) {
            $entityManager->remove($questionAudit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_question_index', [], Response::HTTP_SEE_OTHER);
    }
}

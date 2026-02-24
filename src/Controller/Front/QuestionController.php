<?php

namespace App\Controller\Front;

use App\Entity\Audit;
use App\Entity\QuestionAudit;
use App\Form\QuestionAuditType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/question')]
class QuestionController extends AbstractController
{
    #[Route('/new/{id}', name: 'app_front_question_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Audit $audit, EntityManagerInterface $entityManager): Response
    {
        $question = new QuestionAudit();
        $question->setAudit($audit);
        
        $form = $this->createForm(QuestionAuditType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($question);
            $entityManager->flush();

            return $this->redirectToRoute('app_front_audit_edit', ['id' => $audit->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('front/question/form.html.twig', [
            'question' => $question,
            'form' => $form->createView(),
            'title' => 'Add Question'
        ]);
    }

    #[Route('/{id}/edit', name: 'app_front_question_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, QuestionAudit $question, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(QuestionAuditType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_front_audit_edit', ['id' => $question->getAudit()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('front/question/form.html.twig', [
            'question' => $question,
            'form' => $form->createView(),
            'title' => 'Edit Question'
        ]);
    }

    #[Route('/{id}', name: 'app_front_question_delete', methods: ['POST'])]
    public function delete(Request $request, QuestionAudit $question, EntityManagerInterface $entityManager): Response
    {
        $auditId = $question->getAudit()->getId();
        if ($this->isCsrfTokenValid('delete'.$question->getId(), $request->request->get('_token'))) {
            $entityManager->remove($question);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_front_audit_edit', ['id' => $auditId], Response::HTTP_SEE_OTHER);
    }
}

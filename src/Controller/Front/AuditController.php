<?php

namespace App\Controller\Front;

use App\Entity\Audit;
use App\Entity\AuditAttempt;
use App\Entity\QuestionResponse;
use App\Form\AuditType;
use App\Repository\AuditRepository;
use App\Repository\QuestionAuditRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/audit')]
class AuditController extends AbstractController
{
    #[Route('/', name: 'app_front_audit_index', methods: ['GET'])]
    public function index(AuditRepository $auditRepository): Response
    {
        return $this->render('front/audit/index.html.twig', [
            'audits' => $auditRepository->findBy(['status' => 'public']),
        ]);
    }

    #[Route('/{id}', name: 'app_front_audit_show', methods: ['GET', 'POST'])]
    public function show(Request $request, Audit $audit, EntityManagerInterface $entityManager, QuestionAuditRepository $questionRepository): Response
    {
        if ($request->isMethod('POST')) {
            $participantName = $request->request->get('participant_name');
            $answers = $request->request->all('answers');

            if (empty($participantName)) {
                $this->addFlash('error', 'Please enter your name.');
                return $this->redirectToRoute('app_front_audit_show', ['id' => $audit->getId()]);
            }

            $attempt = new AuditAttempt();
            $attempt->setAudit($audit);
            $attempt->setParticipantName($participantName);

            foreach ($answers as $questionId => $responseValue) {
                // If checkbox isn't checked it might not be sent, but here we expect basic inputs
                // For checkboxes, you might need to handle empty values or use a FormType
                
                $question = $questionRepository->find($questionId);
                if ($question) {
                    $response = new QuestionResponse();
                    $response->setQuestion($question);
                    $response->setResponse((string)$responseValue);
                    $attempt->addResponse($response);
                }
            }

            $entityManager->persist($attempt);
            $entityManager->flush();

            return $this->render('front/audit/success.html.twig', [
                'audit' => $audit,
            ]);
        }

        return $this->render('front/audit/show.html.twig', [
            'audit' => $audit,
        ]);
    }
    #[Route('/manage', name: 'app_front_audit_manage_index', methods: ['GET'])]
    public function manage(AuditRepository $auditRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('front/audit/manage_index.html.twig', [
            'audits' => $auditRepository->findBy(['user' => $user]),
        ]);
    }

    #[Route('/new', name: 'app_front_audit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $audit = new Audit();
        $audit->setUser($user);
        $form = $this->createForm(AuditType::class, $audit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($audit);
            $entityManager->flush();

            return $this->redirectToRoute('app_front_audit_manage_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('front/audit/form.html.twig', [
            'audit' => $audit,
            'form' => $form->createView(),
            'title' => 'Create New Audit'
        ]);
    }

    #[Route('/{id}/edit', name: 'app_front_audit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Audit $audit, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AuditType::class, $audit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_front_audit_manage_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('front/audit/form.html.twig', [
            'audit' => $audit,
            'form' => $form->createView(),
            'title' => 'Edit Audit'
        ]);
    }

    #[Route('/{id}/delete', name: 'app_front_audit_delete', methods: ['POST'])]
    public function delete(Request $request, Audit $audit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$audit->getId(), $request->request->get('_token'))) {
            $entityManager->remove($audit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_front_audit_manage_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/make-public', name: 'app_front_audit_make_public', methods: ['POST'])]
    public function makePublic(Request $request, Audit $audit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('make_public'.$audit->getId(), $request->request->get('_token'))) {
            $audit->setStatus('public');
            $entityManager->flush();
            $this->addFlash('success', 'Audit is now public!');
        }

        return $this->redirectToRoute('app_front_audit_manage_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/mark-completed', name: 'app_front_audit_mark_completed', methods: ['POST'])]
    public function markCompleted(Request $request, Audit $audit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('mark_completed'.$audit->getId(), $request->request->get('_token'))) {
            $audit->setStatus('completed');
            $entityManager->flush();
            $this->addFlash('success', 'Audit marked as completed!');
        }

        return $this->redirectToRoute('app_front_audit_manage_index', [], Response::HTTP_SEE_OTHER);
    }
}

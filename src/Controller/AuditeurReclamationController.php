<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Form\PublicReclamationType;
use App\Repository\ReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[Route('/auditeur/reclamations')]
final class AuditeurReclamationController extends AbstractController
{
    #[Route('', name: 'app_auditeur_reclamations', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        ReclamationRepository $reclamationRepository,
        MailerInterface $mailer
    ): Response {


        //////ajouter reclmation front avec mail
        $reclamation = new Reclamation();
        // Ensure public submissions always start as "en_attente"
        $reclamation->setStatut(Reclamation::STATUT_EN_ATTENTE);

        $form = $this->createForm(PublicReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Statut is enforced server-side for safety
            $reclamation->setStatut(Reclamation::STATUT_EN_ATTENTE);
            $entityManager->persist($reclamation);
            $entityManager->flush();

            // --- Email notification to admin ---
            // TODO: change this to your real admin email
            $adminEmail = 'medlhr0@gmail.com';

            $emailMessage = (new Email())
                ->from($reclamation->getEmail() ?: 'no-reply@example.com')
                ->to($adminEmail)
                ->subject('Nouvelle réclamation d\'un auditeur : ' . $reclamation->getTitre())
                ->html(
                    $this->renderView('emails/reclamation_notification.html.twig', [
                        'reclamation' => $reclamation,
                    ])
                );

            try {
                $mailer->send($emailMessage);
            } catch (\Throwable $e) {
                // On ne casse pas le flux utilisateur si l'envoi d'email échoue
                // Vous pouvez logger l'erreur si nécessaire
            }

            $email = $reclamation->getEmail();

            return $this->redirectToRoute('app_auditeur_reclamations', [
                'email' => $email,
                'success' => 1,
            ], Response::HTTP_SEE_OTHER);
        }

        $emailFilter = $request->query->get('email');
        $myReclamations = [];

        if ($emailFilter) {
            $myReclamations = $reclamationRepository->findBy(
                ['email' => $emailFilter],
                ['dateCreation' => 'DESC']
            );
        }

        $showConfirmationModal = null !== $request->query->get('success');

        return $this->render('auditeur/reclamations.html.twig', [
            'form' => $form->createView(),
            'my_reclamations' => $myReclamations,
            'filter_email' => $emailFilter,
            'show_confirmation_modal' => $showConfirmationModal,
        ]);
    }
}


<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\ChangePasswordType;
use App\Form\EditProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserSpaceController extends AbstractController
{
    #[Route('/espace-utilisateur', name: 'app_user_space')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        $user = $this->getUser();
        
        return $this->render('user_space/index.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/espace-utilisateur/modifier-profil', name: 'app_user_edit_profile')]
    #[IsGranted('ROLE_USER')]
    public function editProfile(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        
        if (!$user instanceof Utilisateur) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(EditProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Votre profil a été mis à jour avec succès !');
            return $this->redirectToRoute('app_user_space');
        }

        return $this->render('user_space/edit_profile.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    #[Route('/espace-utilisateur/changer-mot-de-passe', name: 'app_user_change_password')]
    #[IsGranted('ROLE_USER')]
    public function changePassword(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $this->getUser();
        
        if (!$user instanceof Utilisateur) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            // Vérifier l'ancien mot de passe
            if (!$passwordHasher->isPasswordValid($user, $data['currentPassword'])) {
                $this->addFlash('error', 'Le mot de passe actuel est incorrect.');
                return $this->redirectToRoute('app_user_change_password');
            }

            // Hasher et définir le nouveau mot de passe
            $hashedPassword = $passwordHasher->hashPassword($user, $data['newPassword']);
            $user->setPassword($hashedPassword);
            
            $entityManager->flush();

            $this->addFlash('success', 'Votre mot de passe a été changé avec succès !');
            return $this->redirectToRoute('app_user_space');
        }

        return $this->render('user_space/change_password.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }
}

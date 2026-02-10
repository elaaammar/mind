<?php

namespace App\Form;

use App\Entity\Reclamation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Simplified reclamation form for external users/auditors.
 * Statut is not exposed; it will be set server-side to "en_attente".
 */
class PublicReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre de la réclamation',
                'attr' => [
                    'class' => 'form-control-lg',
                    'placeholder' => 'Ex: Problème avec un audit ou un rapport',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description détaillée',
                'attr' => [
                    'rows' => 5,
                    'placeholder' => 'Expliquez clairement votre réclamation…',
                ],
            ])
            ->add('categorie', TextType::class, [
                'label' => 'Catégorie',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Ex: Audit interne, Audit fournisseur, Qualité…',
                ],
            ])
            ->add('priorite', ChoiceType::class, [
                'label' => 'Priorité',
                'required' => false,
                'choices' => [
                    'Basse' => Reclamation::PRIORITE_BASSE,
                    'Moyenne' => Reclamation::PRIORITE_MOYENNE,
                    'Haute' => Reclamation::PRIORITE_HAUTE,
                ],
                'placeholder' => 'Choisissez une priorité',
            ])
            ->add('nom', TextType::class, [
                'label' => 'Votre nom',
                'attr' => [
                    'placeholder' => 'Votre nom complet',
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Votre email',
                'attr' => [
                    'placeholder' => 'vous@example.com',
                ],
            ])
            ->add('telephone', TextType::class, [
                'label' => 'Téléphone (optionnel)',
                'required' => false,
                'attr' => [
                    'placeholder' => '+216 XX XXX XXX',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
        ]);
    }
}


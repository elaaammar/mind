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
 * Form type for Reclamation entity.
 * No HTML5 validation attributes (required/pattern) - validation is server-side + JS only.
 */
class ReclamationType extends AbstractType
{
    /** @var array<string, string> No required attribute on inputs */
    private const NO_HTML5_VALIDATION = ['required' => false];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre',
                'attr' => self::NO_HTML5_VALIDATION + ['maxlength' => 255, 'placeholder' => 'Titre de la réclamation'],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => self::NO_HTML5_VALIDATION + ['rows' => 4, 'placeholder' => 'Description détaillée'],
            ])
            ->add('statut', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'En attente' => Reclamation::STATUT_EN_ATTENTE,
                    'En cours' => Reclamation::STATUT_EN_COURS,
                    'Résolue' => Reclamation::STATUT_RESOLUE,
                    'Clôturée' => Reclamation::STATUT_CLOTUREE,
                ],
                'attr' => self::NO_HTML5_VALIDATION,
            ])
            ->add('priorite', ChoiceType::class, [
                'label' => 'Priorité',
                'choices' => [
                    'Basse' => Reclamation::PRIORITE_BASSE,
                    'Moyenne' => Reclamation::PRIORITE_MOYENNE,
                    'Haute' => Reclamation::PRIORITE_HAUTE,
                ],
                'required' => false,
                'attr' => self::NO_HTML5_VALIDATION,
            ])
            ->add('categorie', TextType::class, [
                'label' => 'Catégorie',
                'required' => false,
                'attr' => self::NO_HTML5_VALIDATION + ['maxlength' => 100, 'placeholder' => 'Catégorie'],
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'attr' => self::NO_HTML5_VALIDATION + ['maxlength' => 120, 'placeholder' => 'Nom du demandeur'],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => self::NO_HTML5_VALIDATION + ['maxlength' => 180, 'placeholder' => 'email@exemple.com'],
            ])
            ->add('telephone', TextType::class, [
                'label' => 'Téléphone',
                'required' => false,
                'attr' => self::NO_HTML5_VALIDATION + ['maxlength' => 20, 'placeholder' => '+33 6 12 34 56 78'],
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

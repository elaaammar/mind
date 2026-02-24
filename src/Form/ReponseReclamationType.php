<?php

namespace App\Form;

use App\Entity\Reclamation;
use App\Entity\ReponseReclamation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form type for ReponseReclamation entity.
 * No HTML5 validation attributes - validation is server-side + JS only.
 */
class ReponseReclamationType extends AbstractType
{
    /** @var array<string, string> No required attribute on inputs */
    private const NO_HTML5_VALIDATION = ['required' => false];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('reclamation', EntityType::class, [
                'class' => Reclamation::class,
                'choice_label' => function (Reclamation $r) {
                    return sprintf('#%d - %s', $r->getId(), $r->getTitre());
                },
                'label' => 'Réclamation',
                'attr' => self::NO_HTML5_VALIDATION,
                'placeholder' => 'Choisir une réclamation',
            ])
            ->add('contenu', TextareaType::class, [
                'label' => 'Contenu de la réponse',
                'attr' => self::NO_HTML5_VALIDATION + ['rows' => 4, 'placeholder' => 'Contenu de la réponse'],
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom de l\'auteur',
                'attr' => self::NO_HTML5_VALIDATION + ['maxlength' => 120, 'placeholder' => 'Nom'],
            ])
            ->add('auteurType', TextType::class, [
                'label' => 'Type d\'auteur',
                'required' => false,
                'attr' => self::NO_HTML5_VALIDATION + ['maxlength' => 50, 'placeholder' => 'ex: Support, Admin'],
            ])
            ->add('avisUtilisateur', TextareaType::class, [
                'label' => 'Avis utilisateur',
                'required' => false,
                'attr' => self::NO_HTML5_VALIDATION + ['rows' => 2, 'maxlength' => 2000, 'placeholder' => 'Avis optionnel'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ReponseReclamation::class,
        ]);
    }
}

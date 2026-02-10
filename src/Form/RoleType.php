<?php

namespace App\Form;

use App\Entity\Role;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du rôle',
                'attr' => [
                    'placeholder' => 'Ex: Administrateur, Auditeur, Manager...'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'rows' => 4,
                    'placeholder' => 'Description du rôle et de ses responsabilités...'
                ]
            ])
            ->add('permissions', ChoiceType::class, [
                'label' => 'Permissions',
                'choices' => [
                    'Gestion des utilisateurs' => [
                        'Créer des utilisateurs' => 'user.create',
                        'Modifier des utilisateurs' => 'user.edit',
                        'Supprimer des utilisateurs' => 'user.delete',
                        'Voir les utilisateurs' => 'user.view',
                    ],
                    'Gestion des rôles' => [
                        'Créer des rôles' => 'role.create',
                        'Modifier des rôles' => 'role.edit',
                        'Supprimer des rôles' => 'role.delete',
                        'Voir les rôles' => 'role.view',
                    ],
                    'Gestion des audits' => [
                        'Créer des audits' => 'audit.create',
                        'Modifier des audits' => 'audit.edit',
                        'Supprimer des audits' => 'audit.delete',
                        'Voir les audits' => 'audit.view',
                        'Générer des rapports' => 'audit.report',
                    ],
                    'Administration' => [
                        'Accès administration' => 'admin.access',
                        'Configuration système' => 'admin.config',
                    ],
                ],
                'multiple' => true,
                'expanded' => true,
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Role::class,
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\Audit;
use App\Entity\QuestionAudit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestionAuditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextareaType::class, [
                'label' => 'Question Content',
                'attr' => ['class' => 'form-control', 'rows' => 3],
                'label_attr' => ['class' => 'form-label']
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Text' => 'text',
                    'Yes/No' => 'yes_no',
                    'Multiple Choice' => 'multiple_choice',
                ],
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'form-label']
            ])
            ->add('audit', EntityType::class, [
                'class' => Audit::class,
                'choice_label' => 'title',
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'form-label']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => QuestionAudit::class,
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\UserStory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserStoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('subject')
            ->add('content')
            ->add('estimatedTime')
            ->add('dueDate')
            ->add('tags')
            ->add('priority')
            ->add('status')
            ->add('backlog')
            ->add('asignedTo')
            ->add('activity')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'data_class' => UserStory::class,
            "allow_extra_fields" => true

        ]);
    }
}

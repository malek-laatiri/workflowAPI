<?php

namespace App\Form;

use App\Entity\UserStory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
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
            'data_class' => UserStory::class,
        ]);
    }
}
